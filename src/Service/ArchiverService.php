<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Exception\UnsupportedArchiveMethod;

final class ArchiverService
{
    /**
     * @var iterable<ArchiverMethod>
     */
    private iterable $archiverMethods;

    /**
     * @param iterable<ArchiverMethod> $archiverMethods
     */
    public function __construct(iterable $archiverMethods)
    {
        $this->archiverMethods = $archiverMethods;
    }

    /**
     * @param array<File> $files
     */
    public function archive(string $method, string $archiveFilename, array $files): void
    {
        foreach ($this->archiverMethods as $archiverMethod) {
            if ($archiverMethod->supports($method)) {
                $archiverMethod->archive($archiveFilename, $files);

                return;
            }
        }

        throw UnsupportedArchiveMethod::method($method);
    }
}
