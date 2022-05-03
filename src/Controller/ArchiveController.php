<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ArchiverService;
use App\Service\File;
use Cake\Chronos\Chronos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function count;
use function is_array;
use function sprintf;

final class ArchiveController extends AbstractController
{
    private ArchiverService $archiverService;
    private int $maxFilesize;

    public function __construct(ArchiverService $archiverService, int $maxFilesize)
    {
        $this->archiverService = $archiverService;
        $this->maxFilesize = $maxFilesize;
    }

    public function __invoke(Request $request): Response
    {
        /** @var string|null $method */
        $method = $request->request->get('method');
        if (null === $method) {
            return new JsonResponse(['error' => 'Method not provided.'], 400);
        }

        $uploadedFiles = $request->files->get('files');
        if (!is_array($uploadedFiles) || 0 === count($uploadedFiles)) {
            return new JsonResponse(['error' => 'Files not provided.'], 400);
        }

        /** @var array<File> $files */
        $files = [];
        /** @var UploadedFile $uploadedFile */
        foreach ($uploadedFiles as $i => $uploadedFile) {
            if ($uploadedFile->getSize() > $this->maxFilesize) {
                return new JsonResponse(
                    ['error' => sprintf('File "%s" [%d] is too large.', $uploadedFile->getClientOriginalName(), $i)],
                    400
                );
            }
            $files[] = new File(
                $uploadedFile->getClientOriginalName(), $uploadedFile->getPathname(),
            );
        }
        $archiveFilepath = $this->create($method, $files, $request->getClientIp());
        $response = new BinaryFileResponse($archiveFilepath);
        $response->deleteFileAfterSend();

        return $response;
    }

    /**
     * @param array<File> $files
     */
    private function create(string $method, array $files, ?string $ipAddress): string
    {
        $archiveFilepath = tempnam(sys_get_temp_dir(), 'archive_file_');
        $this->archiverService->archive($method, $archiveFilepath, $files, Chronos::now(), $ipAddress);

        return $archiveFilepath;
    }
}
