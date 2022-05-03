<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

final class ArchiveTest extends WebTestCase
{
    public function testItArchivesMultipleFilesIntoSingleZipFile(): void
    {
        $client = self::createClient();
        $parameters = ['method' => 'zip'];
        /** @var array<UploadedFile> $files */
        $files = [
            'files' => [
                new UploadedFile(__DIR__ . '/../Fixtures/files/file1.dat', 'file1.dat'),
                new UploadedFile(__DIR__ . '/../Fixtures/files/file1.dat', 'file1.dat'),
            ],
        ];

        $client->request('POST', '/', $parameters, $files);

        self::assertSame(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode(),
            'Archive endpoint did not respond with HTTP OK.'
        );
        self::assertSame('application/zip', $client->getResponse()->headers->get('content-type'));
    }

}
