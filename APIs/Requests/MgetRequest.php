<?php


declare(strict_types=1);

namespace Sigmie\Base\APIs\Requests;

use Psr\Http\Message\ResponseInterface;
use Sigmie\Base\APIs\Responses\MgetResponse;
use Sigmie\Base\Contracts\ElasticsearchRequest;
use Sigmie\Base\Contracts\ElasticsearchResponse;
use Sigmie\Base\Http\ElasticsearchRequest as HttpElasticsearchRequest;

class MgetRequest extends HttpElasticsearchRequest implements ElasticsearchRequest
{
    public function response(ResponseInterface $psr): ElasticsearchResponse
    {
        return new MgetResponse($psr);
    }
}
