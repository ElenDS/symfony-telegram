<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\ReportGenerator;
use App\Repository\ReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    public function __construct(protected ReportRepository $reportRepository)
    {
    }

    public function generateReport(MessageBusInterface $bus): JsonResponse
    {
        $fileName = uniqid();

        $this->reportRepository->createReport($fileName);

        $bus->dispatch(new ReportGenerator($fileName));

        return new JsonResponse('Report will be created with the name ' . $fileName);
    }
    #[Route("/api/get-report/{filename}")]
    public  function getReport(string $filename): JsonResponse|Response
    {
        $filePath = sprintf('../%s.csv', $filename);
        $filesystem = new Filesystem();

        if($filesystem->exists($filePath)){
            $response = new Response(file_get_contents($filePath));
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename=' . $filePath);

            return $response;
        }

        return new JsonResponse('The file does not exist');
    }
    #[Route("/api/get-report-status/{filename}")]
    public function getReportStatus(string $filename): JsonResponse
    {
        $file = $this->reportRepository->findOneBy(['file_name' => $filename]);

        if(!$file){
            $status = 'not found';
        } else {
            $status = $file->getStatus();
        }

        return new JsonResponse(sprintf('Report status is %s', $status));
    }

}
