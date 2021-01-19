<?php

declare(strict_types=1);

namespace Sigmie\Http\Contracts;

use Sigmie\Http\JSONRequest;
use Sigmie\Http\JsonResponse;

interface JsonClient
{
    public function request(JSONRequest $request): JsonResponse;
}
