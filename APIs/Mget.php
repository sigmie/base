<?php

declare(strict_types=1);

namespace Sigmie\Base\APIs;

use GuzzleHttp\Psr7\Uri;
use Sigmie\Base\Contracts\API;
use Sigmie\Base\Http\Requests\Mget as MgetRequest;
use Sigmie\Base\Http\Responses\Mget as MgetResponse;

trait Mget
{
    use API;

    public function mgetAPICall(string $index, array $body = []): MgetResponse
    {
        $uri = new Uri("/{$index}/_mget");

        $esRequest = new MgetRequest('POST', $uri, $body);

        /* @var MgetResponse */
        return $this->httpCall($esRequest);
    }
}
