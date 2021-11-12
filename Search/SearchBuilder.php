<?php

declare(strict_types=1);

namespace Sigmie\Base\Search;

use Sigmie\Base\Contracts\HttpConnection;
use Sigmie\Base\Contracts\Queries;
use Sigmie\Base\Contracts\QueryClause as Query;
use Sigmie\Base\Search\Queries\Compound\Boolean;
use Sigmie\Base\Search\Queries\MatchAll;
use Sigmie\Base\Search\Queries\MatchNone;
use Sigmie\Base\Search\Queries\Term\Exists;
use Sigmie\Base\Search\Queries\Term\Fuzzy;
use Sigmie\Base\Search\Queries\Term\IDs;
use Sigmie\Base\Search\Queries\Term\Range;
use Sigmie\Base\Search\Queries\Term\Regex;
use Sigmie\Base\Search\Queries\Term\Term;
use Sigmie\Base\Search\Queries\Term\Terms;
use Sigmie\Base\Search\Queries\Term\Wildcard;
use Sigmie\Base\Search\Queries\Text\Match_;
use Sigmie\Base\Search\Queries\Text\MultiMatch;

class SearchBuilder implements Queries
{
    protected Search $search;

    public function __construct(string $index, HttpConnection $httpConnection)
    {
        $this->search = new Search;

        $this->search->index($index)->setHttpConnection($httpConnection);
    }

    public function term(string $field, string|bool $value): Search
    {
        return $this->search->query(new Term($field, $value));
    }

    public function bool(callable $callable): Search
    {
        $query = new Boolean();

        $callable($query);

        return $this->search->query($query);
    }

    public function range(
        string $field,
        array $values = []
    ): Search {

        return $this->search->query(new Range($field, $values));
    }

    public function matchAll(): Search
    {
        return $this->search->query(new MatchAll);
    }

    public function query(Query $query): Search
    {
        return $this->search->query($query);
    }

    public function matchNone(): Search
    {
        return $this->search->query(new MatchNone);
    }

    public function match(string $field, string $query): Search
    {
        return $this->search->query(new Match_($field, $query));
    }

    public function multiMatch(string $query, array $fields = []): Search
    {
        return $this->search->query(new MultiMatch($query, $fields));
    }

    public function exists(string $field): Search
    {
        return $this->search->query(new Exists($field));
    }

    public function ids(array $ids): Search
    {
        return $this->search->query(new IDs($ids));
    }

    public function fuzzy(string $field, string $value): Search
    {
        return $this->search->query(new Fuzzy($field, $value));
    }

    public function terms(string $field, array $values): Search
    {
        return $this->search->query(new Terms($field, $values));
    }

    public function regex(string $field, string $regex): Search
    {
        return $this->search->query(new Regex($field, $regex));
    }

    public function wildcard(string $field, string $value): Search
    {
        return $this->search->query(new Wildcard($field, $value));
    }
}