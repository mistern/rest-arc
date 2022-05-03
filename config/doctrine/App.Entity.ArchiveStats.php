<?php

declare(strict_types=1);

use App\Event\Infrastructure\Doctrine\DBAL\Types\EventIdType;
use App\Event\Infrastructure\Doctrine\DBAL\Types\NameType;
use App\Event\Infrastructure\Doctrine\DBAL\Types\ShortIntroType;
use App\Shared\Infrastructure\Doctrine\DBAL\Types\SlugType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;

/** @var ClassMetadata $metadata */
$builder = new ClassMetadataBuilder($metadata);

$builder->setTable('archiving_stats');

$builder->createField('date', Types::DATE_IMMUTABLE)
    ->makePrimaryKey()
    ->generatedValue('NONE')
    ->build();
$builder->createField('ipAddress', Types::STRING)
    ->length(15)
    ->makePrimaryKey()
    ->generatedValue('NONE')
    ->build();

$builder->createField('count', Types::INTEGER)->build();
