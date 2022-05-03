<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\ArchiveController;
use App\Service\ArchiverService;
use App\Tests\Doubles\ArchiverMethodDouble;
use App\Tests\Doubles\StatsRepositoryDouble;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ArchiveControllerTest extends TestCase
{
    public function testItRespondsWithErrorIfMethodIsNotProvided(): void
    {
        $archiverMethods = [new ArchiverMethodDouble('m1')];
        $archiverService = new ArchiverService($archiverMethods, new StatsRepositoryDouble());
        $controller = new ArchiveController($archiverService, 1);
        $request = new Request();

        $response = $controller($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testItRespondsWithErrorIfFilesAreNotProvided(): void
    {
        $archiverMethods = [new ArchiverMethodDouble('m1')];
        $archiverService = new ArchiverService($archiverMethods, new StatsRepositoryDouble());
        $controller = new ArchiveController($archiverService, 1);
        $request = new Request([], ['method' => 'm1']);

        $response = $controller($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testItRespondsWithErrorIfFileListIsEmptyList(): void
    {
        $archiverMethods = [new ArchiverMethodDouble('m1')];
        $archiverService = new ArchiverService($archiverMethods, new StatsRepositoryDouble());
        $controller = new ArchiveController($archiverService, 1);
        $request = new Request([], ['method' => 'm1'], [], [], ['files' => []], ['REMOTE_ADDR' => '0.0.0.0']);

        $response = $controller($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testItRespondsWithErrorIfFileIsTooLarge(): void
    {
        $archiverMethods = [new ArchiverMethodDouble('m1')];
        $archiverService = new ArchiverService($archiverMethods, new StatsRepositoryDouble());
        $controller = new ArchiveController($archiverService, 1_048_576);
        $request = new Request(
            [],
            ['method' => 'm1'],
            [],
            [],
            ['files' => [new UploadedFile(__DIR__ . '/../../Fixtures/files/file2mb.dat', 'file2mb.dat')]],
            ['REMOTE_ADDR' => '0.0.0.0']
        );

        $response = $controller($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('application/json', $response->headers->get('Content-Type'));
    }
}
