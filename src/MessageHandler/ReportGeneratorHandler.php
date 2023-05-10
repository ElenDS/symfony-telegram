<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\ReportGenerator;
use App\Repository\ReportRepository;
use App\Services\ReportService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class ReportGeneratorHandler extends AsMessageHandler
{
    public function __construct(
        protected ReportService $reportService,
        protected ReportRepository $reportRepository,
        ?string $bus = null,
        ?string $fromTransport = null,
        ?string $handles = null,
        ?string $method = null,
        int $priority = 0
    ) {
        parent::__construct($bus, $fromTransport, $handles, $method, $priority);
    }

    #[AsMessageHandler(fromTransport: 'async', priority: 10)]
     public function __invoke(ReportGenerator $message): void
    {
        $reports = $this->reportService->createReport();
        $filePath = $message->getFilePath();

        $file = fopen($filePath, 'w+');
        foreach ($reports as $report) {
            fputcsv($file, $report, ';');
        }
        fclose($file);

        $filesystem = new Filesystem();
        if($filesystem->exists($filePath)){
            $this->reportRepository->setStatus($message->getFileName(), 'ready');
        }
    }
}