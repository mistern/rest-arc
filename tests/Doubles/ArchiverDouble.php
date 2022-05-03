<?php

declare(strict_types=1);

namespace App\Tests\Doubles;

use App\Service\ArchiverMethod;

final class ArchiverDouble implements ArchiverMethod
{
    public string $method;
    public bool $archived = false;

    public function __construct(string $method)
    {
        $this->method = $method;
    }

    public function supports(string $method): bool
    {
        return $this->method === $method;
    }

    public function archive(string $archiveFilename, array $files): void
    {
        $this->archived = true;
    }
}
