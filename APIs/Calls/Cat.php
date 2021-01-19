<?php

declare(strict_types=1);

namespace Sigmie\Base\APIs\Calls;

use GuzzleHttp\Psr7\Uri;
use Sigmie\Base\Contracts\API;
use Sigmie\Base\Http\ElasticsearchResponse;
use Sigmie\Http\JsonRequest;

trait Cat
{
    use API;

    protected function catAPICall(string $path, string $method): ElasticsearchResponse
    {
        $uri = Uri::withQueryValue(new Uri('/_cat' . $path), 'format', 'json');

        $esRequest = new JsonRequest($method, $uri, []);

        return $this->httpCall($esRequest);
    }
}
