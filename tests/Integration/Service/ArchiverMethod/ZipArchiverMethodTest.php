<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service\ArchiverMethod;

use App\Service\ArchiverMethod\ZipArchiverMethod;
use PHPUnit\Framework\TestCase;
use ZipArchive;

use function aFile;
use function file_put_contents;
use function is_file;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

final class ZipArchiverMethodTest extends TestCase
{
    /**
     * @var array<string>
     */
    private array $filesToRemove = [];

    public function testItCreatesZipArchive(): void
    {
        $filepath = self::getTmpDir() . 'file.dat';
        file_put_contents($filepath, 'File');
        $this->filesToRemove[] = $filepath;
        $archiverMethod = new ZipArchiverMethod();
        $filename = self::getTmpDir() . 'zip_archive.zip';

        $archiverMethod->archive(
            $filename,
            [aFile()->withOriginalFilename('file.dat')->withFilepath($filepath)->build()]
        );

        self::assertTrue(is_file($filename), 'Failed to assert that ZIP archive was created.');

        $this->filesToRemove[] = $filename;
    }

    public function testItCreatesZipArchiveWithProvidedFiles(): void
    {
        $filepath1 = self::getTmpDir() . 'file1.dat';
        file_put_contents($filepath1, 'File 1');
        $this->filesToRemove[] = $filepath1;
        $filepath2 = self::getTmpDir() . 'file2.dat';
        file_put_contents($filepath2, 'File 2');
        $this->filesToRemove[] = $filepath2;
        $files = [
            aFile()->withOriginalFilename($originalFilename1 = 'file1.dat')->withFilepath($filepath1)->build(),
            aFile()->withOriginalFilename($originalFilename2 = 'file2.dat')->withFilepath($filepath2)->build(),
        ];
        $archiverMethod = new ZipArchiverMethod();

        $archiverMethod->archive($archiveFilename = 'archive.zip', $files);
        $this->filesToRemove[] = $archiveFilename;

        $zip = new ZipArchive();
        $zip->open($archiveFilename);
        self::assertNotFalse($zip->locateName($originalFilename1));
        self::assertNotFalse($zip->locateName($originalFilename2));
    }

    protected function tearDown(): void
    {
        foreach ($this->filesToRemove as $fileToRemove) {
            unlink($fileToRemove);
        }
    }

    private static function getTmpDir(): string
    {
        return tempnam(sys_get_temp_dir(), 'zip_archive_test_');
    }
}
