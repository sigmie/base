<?php

declare(strict_types=1);

namespace Sigmie\Base\APIs;

use Sigmie\Base\Contracts\API;
use Sigmie\Base\Contracts\ElasticsearchResponse;
use Sigmie\Base\Http\ElasticsearchRequest;
use Sigmie\Base\Search\Query;

trait Search
{
    use API;

    protected function searchAPICall(Query $query): ElasticsearchResponse
    {
        $uri = $query->uri();

        $esRequest = new ElasticsearchRequest('POST', $uri, $query->toArray());

        return $this->httpCall($esRequest);
    }
}
