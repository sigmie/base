<?php

declare(strict_types=1);

namespace Sigmie\Base\APIs\Calls;

use GuzzleHttp\Psr7\Uri;
use Sigmie\Base\Contracts\API;
use Sigmie\Base\Http\ElasticsearchResponse;
use Sigmie\Http\JsonRequest;

trait Cluster
{
    use API;

    protected function clusterAPICall(string $path): ElasticsearchResponse
    {
        $uri = new Uri('/_cluster' . $path);

        $esRequest = new JsonRequest('GET', $uri, []);

        return $this->call($esRequest);
    }
}