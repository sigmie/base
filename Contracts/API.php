<?php

declare(strict_types=1);

namespace Sigmie\Base\Contracts;

use Sigmie\Base\Http\ElasticsearchResponse;
use Sigmie\Http\Contracts\JSONRequest;

trait API
{
    protected Connection $connection;

    public function setHttpConnection(Connection $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    public function getHttpConnection()
    {
        return $this->connection;
    }

    protected function call(JSONRequest $request, string $responseClass = ElasticsearchResponse::class): ElasticsearchResponse
    {
        return ($this->connection)($request, $responseClass);
    }
}
