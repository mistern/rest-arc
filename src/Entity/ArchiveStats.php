<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;

/**
 * @psalm-suppress MissingConstructor
 */
class ArchiveStats
{
    public DateTimeImmutable $date;
    public string $ipAddress;
    public int $count;
}
