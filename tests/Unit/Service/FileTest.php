<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\File;
use PHPUnit\Framework\TestCase;

final class FileTest extends TestCase
{
    public function testItCreatesWithProvidedOriginalFilename(): void
    {
        $file = new File($expectedFilename = '/provided_file.dat');

        self::assertSame(
            $expectedFilename,
            $file->originalFilename,
            'Failed to assert that file was created with provided original filename.'
        );
    }

    public function testItCreatesNewFileWithChangedOriginalFilename(): void
    {
        $file = new File('/file1.dat');

        $newFile = $file->withOriginalFilename($expectedFilename = '/file2.dat');

        self::assertNotSame($newFile, $file);
        self::assertSame($expectedFilename, $newFile->originalFilename);
    }
}
