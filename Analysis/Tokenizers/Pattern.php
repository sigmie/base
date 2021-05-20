<?php

declare(strict_types=1);

namespace Sigmie\Base\Analysis\Tokenizers;

use Sigmie\Base\Contracts\Configurable;
use Sigmie\Base\Contracts\Tokenizer;

class Pattern implements Configurable, Tokenizer
{
    public function __construct(protected string $pattern)
    {

    }

    public function type(): string
    {
        return 'sigmie_pattern_tokenizer';
    }

    public function name(): string
    {
        return 'sigmie_tokenizer';
    }

    public function config(): array
    {
        return [
            $this->name() => [
                "type" => "pattern",
                "pattern" => $this->pattern
            ]
        ];
    }
}
