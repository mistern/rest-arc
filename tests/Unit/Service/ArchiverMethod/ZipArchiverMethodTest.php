<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\ArchiverMethod;

use App\Service\ArchiverMethod\ZipArchiverMethod;
use PHPUnit\Framework\TestCase;

use function sprintf;

final class ZipArchiverMethodTest extends TestCase
{
    public function testItSupportsZipMethod(): void
    {
        $archiverMethod = new ZipArchiverMethod();

        $supports = $archiverMethod->supports(ZipArchiverMethod::METHOD);

        self::assertTrue(
            $supports,
            sprintf('Failed to assert that ZIP archiver supports "%s" method.', ZipArchiverMethod::METHOD)
        );
    }

    public function testItDoesNotSupportOtherMethodThanZip(): void
    {
        $archiverMethod = new ZipArchiverMethod();
        $supports = $archiverMethod->supports($unsupportedMethod = ZipArchiverMethod::METHOD . 'a');

        self::assertFalse(
            $supports,
            sprintf('Failed to assert that ZIP archiver does not support "%s" method.', $unsupportedMethod)
        );
    }
}
