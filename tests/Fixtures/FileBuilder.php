<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Service\File;

final class FileBuilder
{
    private string $originalFilename;

    public function __construct()
    {
        $this->originalFilename = 'file.dat';
    }

    public function withOriginalFilename(string $originalFilename): self
    {
        $new = clone $this;
        $new->originalFilename = $originalFilename;

        return $new;
    }

    public function build(): File
    {
        return new File($this->originalFilename);
    }
}
