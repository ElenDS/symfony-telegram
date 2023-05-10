<?php

declare(strict_types=1);

namespace App\Command;

use App\Services\Telegram\LongmanClient;
use App\Services\Telegram\UpdateManager;
use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:telegram-bot-command',
    description: 'get telegram updates',
)]
class TelegramBotCommand extends Command
{
    public function __construct(protected LongmanClient $longmanClient, protected UpdateManager $updateManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $telegram = $this->longmanClient->createApiObject();
        } catch (TelegramException $exception) {
            echo $exception->getMessage();
            exit;
        }

        while (true) {
            $updates = $telegram->handleGetUpdates()->getResult();
            if (!empty($updates)) {
                $this->updateManager->handler($updates);
            } else {
                sleep(2);
            }
        }
    }
}
