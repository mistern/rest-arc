<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Service\File;

final class FileBuilder
{
    private string $originalFilename;
    private string $filepath;

    public function __construct()
    {
        $this->originalFilename = 'original_filename.dat';
        $this->filepath = '/filepath.dat';
    }

    public function withOriginalFilename(string $originalFilename): self
    {
        $new = clone $this;
        $new->originalFilename = $originalFilename;

        return $new;
    }

    public function withFilepath(string $filepath): self
    {
        $new = clone $this;
        $new->filepath = $filepath;

        return $new;
    }


    public function build(): File
    {
        return new File($this->originalFilename, $this->filepath);
    }
}
