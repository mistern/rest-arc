<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\ArchiverService;
use App\Service\Exception\UnsupportedArchiveMethod;
use App\Service\File;
use App\Tests\Doubles\ArchiverDouble;
use PHPUnit\Framework\TestCase;

final class ArchiverServiceTest extends TestCase
{
    public function testItArchivesFilesUsingProvidedMethod(): void
    {
        $methods = [$method1 = new ArchiverDouble('m1')];
        $service = new ArchiverService($methods);

        $service->archive('m1', 'archive.m1', [new File()]);

        self::assertTrue($method1->archived, 'Failed to assert that files were archived using provided method.');
    }

    public function testItDoesntArchivesFilesUsingUnprovidedMethod(): void
    {
        $methods = [$method1 = new ArchiverDouble('m1'), new ArchiverDouble('m2')];
        $service = new ArchiverService($methods);

        $service->archive('m2', 'archive.m2', [new File()]);

        self::assertFalse($method1->archived, 'Failed to assert that files were not archived using unprovided method.');
    }

    public function testItFailsToArchiveFilesIfMethodIsNotSupported(): void
    {
        $service = new ArchiverService([]);

        $this->expectExceptionObject(UnsupportedArchiveMethod::method($method = 'unsupported'));

        $service->archive($method, 'archive.unsupported', [new File()]);
    }
}
