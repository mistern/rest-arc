<?php

declare(strict_types=1);

namespace App\Service;

/**
 * @psalm-immutable
 */
final class File
{
    public string $originalFilename;

    public function __construct(string $originalFilename)
    {
        $this->originalFilename = $originalFilename;
    }

    public function withOriginalFilename(string $originalFilename): self
    {
        $new = clone $this;
        $new->originalFilename = $originalFilename;

        return $new;
    }
}
