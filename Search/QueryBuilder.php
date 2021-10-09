<?php

declare(strict_types=1);

namespace Sigmie\Base\Search;

use Closure;
use Sigmie\Base\APIs\Search as SearchAPI;
use Sigmie\Base\Http\ElasticsearchResponse;
use Sigmie\Base\Index\AbstractIndex;
use Sigmie\Base\Search\Clauses\Boolean;
use Sigmie\Base\Search\Clauses\Filtered;
use Sigmie\Base\Search\Clauses\Match_;
use Sigmie\Base\Search\Clauses\Query as QueryClause;
use Sigmie\Base\Search\Compound\Boolean as CompoundBoolean;
use Sigmie\Base\Search\Queries\Term as QueriesTerm;
use Sigmie\Base\Search\Term\Term;
use Sigmie\Http\Contracts\JSONRequest;

class QueryBuilder
{
    private $queries;

    public function __construct(protected SearchBuilder $searchBuilder)
    {
    }

    public function matchAll()
    {
        return;
    }

    public function match($filed, $value)
    {
        $this->queries[] = new Match_;

        return $this->query->match($filed, $value);
    }

    public function multiMatch()
    {
        return;
    }

    public function term($field, $value)
    {
        $this->query = new QueriesTerm($this);

        return $this->query->term($field, $value);
    }

    public function range()
    {
        return;
    }

    public function bool(callable $callable)
    {
        $this->query = new CompoundBoolean;

        $callable($this->query);

        return $this->searchBuilder;
    }

    public function toRaw()
    {
        return $this->query->toRaw();
    }
}
