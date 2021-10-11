<?php

declare(strict_types=1);

namespace Sigmie\Base\Mappings\Types;

use Sigmie\Base\Contracts\Analyzer;
use Sigmie\Base\Contracts\CustomAnalyzer as AnalyzerInterface;
use Sigmie\Base\Mappings\PropertyType;

class Text extends PropertyType
{
    protected ?Analyzer $analyzer;

    public function __construct(
        protected string $name,
        protected bool $keyword = false
    ) {
    }

    public function searchAsYouType(Analyzer $analyzer = null): self
    {
        $this->analyzer = $analyzer;
        $this->type = 'search_as_you_type';

        return $this;
    }

    public function unstructuredText(Analyzer $analyzer = null): self
    {
        $this->analyzer = $analyzer;
        $this->type = 'text';

        return $this;
    }

    public function completion(Analyzer $analyzer = null): self
    {
        $this->analyzer = $analyzer;
        $this->type = 'completion';

        return $this;
    }

    public function withAnalyzer(Analyzer $analyzer): void
    {
        $this->analyzer = $analyzer;
    }

    public function analyzer(): ?AnalyzerInterface
    {
        return $this->analyzer;
    }

    public function toRaw(): array
    {
        $raw = [
            $this->name => [
                'type' => $this->type,
            ]
        ];


        if ($this->keyword) {
            $raw[$this->name]['fields'] = ['keyword' => ['type' => 'keyword']];
        }

        if (!is_null($this->analyzer)) {
            $raw[$this->name]['analyzer'] = $this->analyzer->name();
        }

        return $raw;
    }
}
