<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\ArchiverMethod;
use App\Service\ArchiverService;
use App\Service\Exception\UnsupportedArchiveMethod;
use App\Service\StatsRepository;
use App\Tests\Doubles\ArchiverDouble;
use App\Tests\Doubles\StatsRepositoryDouble;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\TestCase;

use function aFile;

final class ArchiverServiceTest extends TestCase
{
    public function testItArchivesFilesUsingProvidedMethod(): void
    {
        $methods = [$method1 = new ArchiverDouble('m1')];
        $service = self::createArchiverService($methods);

        $service->archive('m1', 'archive.m1', [aFile()->build()], Chronos::now(), '0.0.0.0');

        self::assertTrue($method1->archived, 'Failed to assert that files were archived using provided method.');
    }

    public function testItDoesntArchivesFilesUsingUnprovidedMethod(): void
    {
        $methods = [$method1 = new ArchiverDouble('m1'), new ArchiverDouble('m2')];
        $service = self::createArchiverService($methods);

        $service->archive('m2', 'archive.m2', [aFile()->build()], Chronos::now(), '0.0.0.0');

        self::assertFalse($method1->archived, 'Failed to assert that files were not archived using unprovided method.');
    }

    public function testItFailsToArchiveFilesIfMethodIsNotSupported(): void
    {
        $service = self::createArchiverService([]);

        $this->expectExceptionObject(UnsupportedArchiveMethod::method($method = 'unsupported'));

        $service->archive($method, 'archive.unsupported', [aFile()->build()], Chronos::now(), '0.0.0.0');
    }

    public function testItCreatesArchiveWithProvidedFilename(): void
    {
        $methods = [$method = new ArchiverDouble('m1')];
        $service = self::createArchiverService($methods);

        $service->archive('m1', $expectedFilename = 'archive.m1', [aFile()->build()], Chronos::now(), '0.0.0.0');

        self::assertSame(
            $expectedFilename,
            $method->archiveFilename,
            'Failed to assert that archive was created with provided filename.'
        );
    }

    public function testItCreatesArchiveWithProvidedFiles(): void
    {
        $methods = [$method = new ArchiverDouble('m1')];
        $service = self::createArchiverService($methods);

        $service->archive(
            'm1',
            'archive.m1',
            $expectedFiles = [
                aFile()->withOriginalFilename('/file1.dat')->build(),
                aFile()->withOriginalFilename('/file2.dat')->build(),
            ],
            Chronos::now(),
            '0.0.0.0'
        );

        self::assertEquals(
            $expectedFiles,
            $method->files,
            'Failed to assert that archive was created with provided filename.'
        );
    }

    public function testItCreatesArchiveWithDeduplicatedFiles(): void
    {
        $methods = [$method = new ArchiverDouble('m1')];
        $service = self::createArchiverService($methods);

        $service->archive(
            'm1',
            'archive.m1',
            [
                aFile()->withOriginalFilename('/dedupe_file.dat')->build(),
                aFile()->withOriginalFilename('/dedupe_file.dat')->build(),
            ],
            Chronos::now(),
            '0.0.0.0'
        );

        $expectedFiles = [
            aFile()->withOriginalFilename('/dedupe_file.dat')->build(),
            aFile()->withOriginalFilename('/dedupe_file(2).dat')->build(),
        ];
        self::assertEquals(
            $expectedFiles,
            $method->files,
            'Failed to assert that archive was created with deduplicated files.'
        );
    }

    public function testItTracksDateTimeOfArchivingRequest(): void
    {
        $methods = [new ArchiverDouble('m1')];
        $statsRepository = new StatsRepositoryDouble();
        $service = new ArchiverService($methods, $statsRepository);

        $expectedDateTime = Chronos::create(2000, 1, 1, 0, 0, 0, 0);

        $service->archive('m1', 'archive.m1', [aFile()->build()], $expectedDateTime, '0.0.0.0');

        self::assertEquals(
            $expectedDateTime,
            $statsRepository->dateTime,
            'Failed to assert that date/time of archiving request was tracked.'
        );
    }

    public function testItTracksIpAddressOfArchivingRequest(): void
    {
        $methods = [new ArchiverDouble('m1')];
        $statsRepository = new StatsRepositoryDouble();
        $service = new ArchiverService($methods, $statsRepository);

        $service->archive('m1', 'archive.m1', [aFile()->build()], Chronos::now(), $expectedIpAddress = '0.0.0.0');

        self::assertSame(
            $expectedIpAddress,
            $statsRepository->ipAddress,
            'Failed to assert that IP address of archiving request was tracked.'
        );
    }

    /**
     * @param array<ArchiverMethod> $methods
     */
    private static function createArchiverService(
        array $methods = null,
        StatsRepository $statsRepository = null
    ): ArchiverService {
        $methods = null !== $methods
            ? $methods
            : [new ArchiverDouble('m1')];
        $statsRepository = null !== $statsRepository
            ? $statsRepository
            : new StatsRepositoryDouble();

        return new ArchiverService($methods, $statsRepository);
    }
}
