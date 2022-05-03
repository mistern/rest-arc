<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;

interface StatsRepository
{
    public function track(DateTimeImmutable $dateTime, string $ipAddress): void;
}
