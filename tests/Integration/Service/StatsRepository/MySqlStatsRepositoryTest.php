<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service\StatsRepository;

use App\Service\StatsRepository\MySqlStatsRepository;
use Cake\Chronos\Chronos;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class MySqlStatsRepositoryTest extends KernelTestCase
{
    private ?Connection $connection = null;

    public function testItCreatesNewEntry(): void
    {
        $connection = $this->getConnection();
        $repository = new MySqlStatsRepository($connection);

        $repository->track(Chronos::create(2000, 1, 1, 1, 1, 1, 1), '0.0.0.0');

        $rows = $connection->fetchAllAssociative(
            <<<SQL
                SELECT date, ip_address, count 
                FROM archiving_stats
            SQL
        );
        self::assertCount(1, $rows);
        $row = $rows[0];
        self::assertSame('2000-01-01', $row['date'], 'Failed to assert that date of archiving stats was populated.');
        self::assertSame(
            '0.0.0.0',
            $row['ip_address'],
            'Failed to assert that IP address of archiving stats was populated.'
        );
        self::assertSame('1', $row['count'], 'Failed to assert that count of archiving stats was started.');
    }

    public function testItIncrementsCountForEntryWithSameDateAndIpAddress(): void
    {
        $connection = $this->getConnection();
        $repository = new MySqlStatsRepository($connection);
        $repository->track(Chronos::create(2000, 1, 1, 1, 1, 1, 1), '0.0.0.0');

        $repository->track(Chronos::create(2000, 1, 1, 1, 1, 1, 1), '0.0.0.0');

        $rows = $connection->fetchAllAssociative(
            <<<SQL
                SELECT date, ip_address, count 
                FROM archiving_stats
            SQL
        );
        self::assertCount(1, $rows);
        $row = $rows[0];
        self::assertSame('2', $row['count'], 'Failed to assert that count of archiving stats was incremented.');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (null !== $this->connection) {
            $this->connection->close();
            $this->connection = null;
        }
    }

    private function getConnection(): Connection
    {
        if (null === $this->connection) {
            $this->connection = self::bootKernel()->getContainer()->get('doctrine.dbal.default_connection');
        }

        return $this->connection;
    }
}
