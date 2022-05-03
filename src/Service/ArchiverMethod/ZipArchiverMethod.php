<?php

declare(strict_types=1);

namespace App\Service\ArchiverMethod;

use App\Service\ArchiverMethod;
use ZipArchive;

final class ZipArchiverMethod implements ArchiverMethod
{
    public const METHOD = 'zip';

    public function supports(string $method): bool
    {
        return self::METHOD === $method;
    }

    public function archive(string $archiveFilename, array $files): void
    {
        $zip = new ZipArchive();
        $zip->open($archiveFilename, ZipArchive::CREATE);
        foreach ($files as $file) {
            $zip->addFile($file->filepath, $file->originalFilename);
        }
        $zip->close();
    }
}
