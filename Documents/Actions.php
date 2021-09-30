<?php

declare(strict_types=1);

namespace Sigmie\Base\Documents;

use function Amp\Parallel\Worker\enqueue;
use function Amp\Promise\all;
use function Amp\Promise\wait;
use Exception;
use Sigmie\Base\APIs\Bulk as BulkAPI;
use Sigmie\Base\APIs\Delete as DeleteAPI;
use Sigmie\Base\APIs\Mget as MgetAPI;
use Sigmie\Base\APIs\Search as SearchAPI;
use Sigmie\Base\APIs\Update as UpdateAPI;
use Sigmie\Base\Contracts\API;
use Sigmie\Base\Contracts\DocumentCollection as DocumentCollectionInterface;
use Sigmie\Base\Documents\DocumentCollection;
use Sigmie\Base\Index\Index;
use Sigmie\Base\Search\Query;
use Sigmie\Support\BulkBody;

trait Actions
{
    use SearchAPI, DeleteAPI, MgetAPI, BulkAPI, UpdateAPI, API;

    abstract private function index(): Index;

    public function updateDocument(Document $document): Document
    {
        $body = [
            ['update' => ['_id' => $document->_id]],
            ['doc' => $document->_source],
        ];

        $response = $this->bulkAPICall($this->index()->name, $body);

        if ($response->failed()) {
            throw new Exception('Document update failed.');
        }

        return $document;
    }

    protected function upsertDocuments(DocumentCollectionInterface $collection): DocumentCollectionInterface
    {
        $indexName = $this->index()->name;
        $body = [];
        $collection->each(function (Document $document, $index) use (&$body) {
            $body = [
                ...$body,
                ['update' => ($document->_id !== null) ? ['_id' => $document->_id] : (object) []],
                ['doc' => $document->_source, 'doc_as_upsert' => true],
            ];
        });

        $this->bulkAPICall($indexName, $body);

        return $collection;
    }

    /**
     * @param bool $async Should we wait for the
     * document to become available
     */
    protected function createDocument(Document $doc, bool $async): Document
    {
        $indexName = $this->index()->name;
        $array = [];

        if ($doc->_id !== null) {
            $array = ['_id' => $doc->_id];
        }

        $data = [
            ['create' => (object) $array],
            $doc->_source,
        ];

        $res = $this->bulkAPICall($indexName, $data, $async);

        $data = $res->getAll()->first()['create'];

        if (is_null($doc->_id)) {
            $doc->_id = $data['_id'];
        }

        return $doc;
    }

    protected function createDocuments(DocumentCollection $documentCollection, bool $async): DocumentCollection
    {
        $indexName = $this->index()->name;
        $body = [];
        $docs = $documentCollection->toArray();

        $docsChunk = array_chunk($docs, 2);

        $promises = [];
        foreach ($docsChunk as $docs) {
            $promises[] = enqueue(new BulkBody($docs));
        }

        $all = wait(all($promises));

        $body = array_merge(...$all);

        $response = $this->bulkAPICall($indexName, $body, $async);

        $ids = $response->getAll()->map(fn ($value) => $value['create']['_id']);

        $index = 0;
        return $documentCollection->each(function (Document $doc) use ($ids, &$index) {
            if (is_null($doc->_id)) {
                $doc->_id = $ids[$index];
            }
            $index++;
        });

        return $documentCollection;
    }

    protected function getDocument(string $identifier): ?Document
    {
        $response = $this->mgetAPICall($this->name, ['docs' => [['_id' => $identifier]]]);

        return $response->first();
    }

    protected function listDocuments(int $offset = 0, int $limit = 100): DocumentCollection
    {
        $response = $this->searchAPICall($this->index()->name, [
            'from' => $offset, 'size' => $limit,
            'query' => ['match_all' => (object) []]
        ]);

        $collection = new DocumentCollection();

        $values = $response->json('hits')['hits'];

        foreach ($values as $data) {
            $doc = new Document($data['_source'], $data['_id']);
            $collection->addDocument($doc);
        }

        return $collection;
    }

    protected function deleteDocument(string $identifier): bool
    {
        $response = $this->deleteAPICall(
            identifier: $identifier,
        );

        return $response->json('result') === 'deleted';
    }

    protected function deleteDocuments(array $ids): bool
    {
        $indexName = $this->index()->name;

        $body = [];
        foreach ($ids as $id) {
            $body = [
                ...$body,
                ['delete' => ['_index' => $indexName, '_id' => $id]],
            ];
        }
        $response = $this->bulkAPICall($indexName, $body);

        return $response->failed() === false;
    }
}
