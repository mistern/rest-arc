<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\ArchiverService;
use App\Service\Exception\UnsupportedArchiveMethod;
use App\Tests\Doubles\ArchiverDouble;
use PHPUnit\Framework\TestCase;

final class ArchiverServiceTest extends TestCase
{
    public function testItArchivesFilesUsingProvidedMethod(): void
    {
        $methods = [$method1 = new ArchiverDouble('m1')];
        $service = new ArchiverService($methods);

        $service->archive('m1', 'archive.m1', [aFile()->build()]);

        self::assertTrue($method1->archived, 'Failed to assert that files were archived using provided method.');
    }

    public function testItDoesntArchivesFilesUsingUnprovidedMethod(): void
    {
        $methods = [$method1 = new ArchiverDouble('m1'), new ArchiverDouble('m2')];
        $service = new ArchiverService($methods);

        $service->archive('m2', 'archive.m2', [aFile()->build()]);

        self::assertFalse($method1->archived, 'Failed to assert that files were not archived using unprovided method.');
    }

    public function testItFailsToArchiveFilesIfMethodIsNotSupported(): void
    {
        $service = new ArchiverService([]);

        $this->expectExceptionObject(UnsupportedArchiveMethod::method($method = 'unsupported'));

        $service->archive($method, 'archive.unsupported', [aFile()->build()]);
    }

    public function testItCreatesArchiveWithProvidedFilename(): void
    {
        $methods = [$method = new ArchiverDouble('m1')];
        $service = new ArchiverService($methods);

        $service->archive('m1', $expectedFilename = 'archive.m1', [aFile()->build()]);

        self::assertSame(
            $expectedFilename,
            $method->archiveFilename,
            'Failed to assert that archive was created with provided filename.'
        );
    }

    public function testItCreatesArchiveWithProvidedFiles(): void
    {
        $methods = [$method = new ArchiverDouble('m1')];
        $service = new ArchiverService($methods);

        $service->archive(
            'm1',
            'archive.m1',
            $expectedFiles = [
                aFile()->withOriginalFilename('/file1.dat')->build(),
                aFile()->withOriginalFilename('/file2.dat')->build(),
            ]
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
        $service = new ArchiverService($methods);

        $service->archive(
            'm1', 'archive.m1', [
                aFile()->withOriginalFilename('/dedupe_file.dat')->build(),
                aFile()->withOriginalFilename('/dedupe_file.dat')->build(),
            ]
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
}
