<?php

declare(strict_types=1);

namespace App\Service\StatsRepository;

use App\Service\StatsRepository;
use Cake\Chronos\Date;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

final class MySqlStatsRepository implements StatsRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function track(DateTimeImmutable $dateTime, string $ipAddress): void
    {
        $date = Date::instance($dateTime)->setTimezone('UTC');
        $this->connection->executeStatement(
            <<<SQL
                INSERT INTO archiving_stats
                SET date = :date, ip_address = :ipAddress, count = 1
                ON DUPLICATE KEY UPDATE count = count + 1
            SQL, ['date' => $date->format('Y-m-d'), 'ipAddress' => $ipAddress]
        );
    }
}
