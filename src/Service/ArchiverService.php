<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Exception\UnsupportedArchiveMethod;
use DateTimeImmutable;

use function pathinfo;
use function sprintf;

use const PATHINFO_DIRNAME;
use const PATHINFO_EXTENSION;
use const PATHINFO_FILENAME;

final class ArchiverService
{
    /**
     * @var iterable<ArchiverMethod>
     */
    private iterable $archiverMethods;
    private StatsRepository $statsRepository;

    /**
     * @param iterable<ArchiverMethod> $archiverMethods
     */
    public function __construct(iterable $archiverMethods, StatsRepository $statsRepository)
    {
        $this->archiverMethods = $archiverMethods;
        $this->statsRepository = $statsRepository;
    }

    /**
     * @param array<File> $files
     */
    public function archive(
        string $method,
        string $archiveFilename,
        array $files,
        DateTimeImmutable $dateTime,
        string $ipAddress
    ): void {
        $deduplicatedFiles = self::deduplicateFiles($files);
        foreach ($this->archiverMethods as $archiverMethod) {
            if ($archiverMethod->supports($method)) {
                $archiverMethod->archive($archiveFilename, $deduplicatedFiles);
                $this->statsRepository->track($dateTime, $ipAddress);

                return;
            }
        }

        throw UnsupportedArchiveMethod::method($method);
    }

    /**
     * @param array<File> $files
     * @return array<File>
     */
    private static function deduplicateFiles(array $files): array
    {
        /** @var array<File> $deduplicatedFiles */
        $deduplicatedFiles = [];

        /** @var array<string, int> $filenames */
        $filenames = [];
        foreach ($files as $file) {
            $originalFilename = $file->originalFilename;
            if (!isset($filenames[$originalFilename])) {
                $filenames[$originalFilename] = 1;
                $deduplicatedFiles[] = $file;
                continue;
            }

            $originalFileDirname = pathinfo($originalFilename, PATHINFO_DIRNAME);
            $originalFileBasename = pathinfo($originalFilename, PATHINFO_FILENAME);
            $originalFileExtension = pathinfo($originalFilename, PATHINFO_EXTENSION);
            $deduplicatedFilename = sprintf(
                '%s%s(%d).%s',
                $originalFileDirname,
                $originalFileBasename,
                ++$filenames[$originalFilename],
                $originalFileExtension
            );
            $deduplicatedFiles[] = $file->withOriginalFilename($deduplicatedFilename);
        }

        return $deduplicatedFiles;
    }
}
