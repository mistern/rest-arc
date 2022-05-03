<?php

declare(strict_types=1);

namespace App\Service\Exception;

use InvalidArgumentException;

use function sprintf;

final class UnsupportedArchiveMethod extends InvalidArgumentException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function method(string $method): self
    {
        return new self(sprintf('Unsupported archive method "%s".', $method));
    }
}
