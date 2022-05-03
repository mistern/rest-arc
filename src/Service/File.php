<?php

declare(strict_types=1);

namespace App\Service;

/**
 * @psalm-immutable
 */
final class File
{
    public string $originalFilename;
    public string $filepath;

    public function __construct(string $originalFilename, string $filepath)
    {
        $this->originalFilename = $originalFilename;
        $this->filepath = $filepath;
    }

    public function withOriginalFilename(string $originalFilename): self
    {
        $new = clone $this;
        $new->originalFilename = $originalFilename;

        return $new;
    }
}
