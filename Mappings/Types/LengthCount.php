<?php

declare(strict_types=1);

namespace Sigmie\Base\Mappings\Types;

use Sigmie\Base\Contracts\Type;

class LengthCount implements Type
{
    public function field(): string
    {
        return 'token_count';
    }
}
