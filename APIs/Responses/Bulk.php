<?php

declare(strict_types=1);

namespace Sigmie\Base\APIs\Responses;

use Exception;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Sigmie\Base\Contracts\ElasticsearchRequest;
use Sigmie\Base\Exceptions\BulkException;
use Sigmie\Base\Http\ElasticsearchResponse;
use Sigmie\Support\Collection;
use Sigmie\Support\Contracts\Collection as CollectionInterface;

class Bulk extends ElasticsearchResponse
{
    protected CollectionInterface $successCollection;

    protected CollectionInterface $failCollection;

    protected int $took;

    public function __construct(ResponseInterface $psr)
    {
        parent::__construct($psr);

        $this->successCollection = new Collection();
        $this->failCollection = new Collection();

        $this->createCollections($this->json('items'));
    }

    public function exception(ElasticsearchRequest $request): Exception
    {
        return new BulkException($this->failCollection);
    }

    public function getAll()
    {
        return new Collection([...$this->successCollection, ...$this->failCollection]);
    }

    public function getFailed(): Collection
    {
        return $this->failCollection;
    }

    public function getSuccessful(): Collection
    {
        return $this->successCollection;
    }

    public function failed(): bool
    {
        return parent::failed() || $this->json('errors');
    }

    private function createCollections(array $items)
    {
        foreach ($items as $data) {
            [$action] = array_keys($data);
            [$values] = array_values($data);

            if (isset($values['error'])) {
                $this->failCollection->add([$action, $values]);
                continue;
            }

            $this->successCollection->add([$action, $values]);
        }
    }
}
