<?php

declare(strict_types=1);

use App\Tests\Fixtures\FileBuilder;

function aFile(): FileBuilder
{
    return new FileBuilder();
}
