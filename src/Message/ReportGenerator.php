<?php

declare(strict_types=1);

namespace App\Message;

class ReportGenerator
{
    private string $filePath;
    public function __construct(
        private readonly string $fileName,
    ) {
        $this->filePath = sprintf('%s.csv', $this->fileName);
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
    public function getFileName():string
    {
        return $this->fileName;
    }
}