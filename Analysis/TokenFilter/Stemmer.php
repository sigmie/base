<?php

declare(strict_types=1);

namespace Sigmie\Base\Analysis\TokenFilter;

use Sigmie\Base\Contracts\TokenFilter;

class Stemmer implements TokenFilter
{
    protected string $name = 'stemmer_overrides';

    public function __construct(
        protected string $prefix,
        protected array $stems
    ) {
        $this->name = "{$prefix}_{$this->name}";
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function type(): string
    {
        return 'stemmer_override';
    }

    public static function fromRaw(array $raw)
    {
        $instance = new static('', $raw['rules']);

        return $instance;
    }

    public function value(): array
    {
        $rules = [];
        foreach ($this->stems as $to => $from) {
            $from = implode(', ', $from);
            $rules[] = "{$from} => {$to}";
        }

        return [
            'rules' => $rules,
            'class' => static::class
        ];
    }
}
