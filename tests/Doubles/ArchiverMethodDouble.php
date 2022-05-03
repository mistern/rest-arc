<?php

declare(strict_types=1);

namespace App\Tests\Doubles;

use App\Service\ArchiverMethod;
use App\Service\File;

final class ArchiverMethodDouble implements ArchiverMethod
{
    public string $method;
    public bool $archived = false;
    public ?string $archiveFilename = null;
    /**
     * @var array<File>
     */
    public array $files = [];

    public function __construct(string $method)
    {
        $this->method = $method;
    }

    public function supports(string $method): bool
    {
        return $this->method === $method;
    }

    public function archive(string $archiveFilename, array $files): void
    {
        $this->archiveFilename = $archiveFilename;
        $this->files = $files;
        $this->archived = true;
    }
}
