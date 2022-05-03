<?php

declare(strict_types=1);

namespace App\Service;

interface ArchiverMethod
{
    public function supports(string $method): bool;

    /**
     * @param array<File> $files
     */
    public function archive(string $archiveFilename, array $files): void;
}
