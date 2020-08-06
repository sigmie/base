<?php

declare(strict_types=1);


namespace Sigma\Mapping\Types;

use Sigma\Mapping\Type;

/**
 * @Annotation
 */
class Double extends Type
{
    public function field(): string
    {
        return 'double';
    }
}
