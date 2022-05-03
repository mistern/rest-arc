<?php

declare(strict_types=1);

namespace App\Tests\Doubles;

use App\Service\StatsRepository;
use DateTimeImmutable;

final class StatsRepositoryDouble implements StatsRepository
{
    public ?DateTimeImmutable $dateTime = null;
    public ?string $ipAddress = null;

    public function track(DateTimeImmutable $dateTime, string $ipAddress): void
    {
        $this->dateTime = $dateTime;
        $this->ipAddress = $ipAddress;
    }
}
