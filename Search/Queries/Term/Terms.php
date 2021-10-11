<?php

declare(strict_types=1);

namespace Sigmie\Base\Search\Queries\Term;

use Sigmie\Base\Search\Queries\QueryClause;

class Terms extends QueryClause
{
    public function __construct(
        protected string $field,
        protected array $values
    ) {
    }

    public function toRaw(): array
    {
        return [
            'terms' => [
                $this->field => $this->values
            ]
        ];
    }
}
