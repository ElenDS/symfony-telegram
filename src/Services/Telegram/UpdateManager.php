<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use App\Entity\TelegramChat;
use App\Services\Quiz\QuizSender;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Longman\TelegramBot\Exception\TelegramException;

class UpdateManager
{
    protected ObjectRepository $chatRepository;

    public function __construct(
        ManagerRegistry $doctrine,
        protected QuizSender $quizSender
    ) {
        $this->chatRepository = $doctrine->getRepository(TelegramChat::class);
    }

    /**
     * @throws TelegramException
     */
    public function handler($updates): void
    {
        foreach ($updates as $update) {
            $telegramId = intval($update->getMessage()->getChat()->getId());
            $message = $update->getMessage()->getText();

            $chat = $this->getTelegramChat($telegramId);

            match (true) {
                $message === BotCommands::START => $this->askTypeOfQuiz($telegramId),
                $message === BotCommands::GET_MATH_QUIZ
                || $message === BotCommands::GET_THEORETIC_QUIZ => $this->startQuiz($chat, substr($message, 1)),
                $message === BotCommands::GET_SCORE => $this->getScore($chat),
                default => $this->processAnswer($message, $chat)
            };
        }
    }

    private function getTelegramChat(int $telegramId): TelegramChat
    {
        $chat = $this->chatRepository->findOneBy(['telegram_id' => $telegramId]);
        if (!$chat) {
            $chat = $this->chatRepository->createTelegramChat($telegramId);
        }
        return $chat;
    }

    /**
     * @throws TelegramException
     */
    private function startQuiz(TelegramChat $chat, string $type): void
    {
        $this->quizSender->sendNewQuiz($type, $chat);
    }

    /**
     * @throws TelegramException
     */
    private function askTypeOfQuiz(int $telegramId): void
    {
        $this->quizSender->askType($telegramId);
    }

    /**
     * @throws TelegramException
     */
    private function processAnswer(string $answer, TelegramChat $chat): void
    {
        $this->quizSender->replyToAnswer($chat, $answer);

        $this->askTypeOfQuiz($chat->getTelegramId());
    }

    /**
     * @throws TelegramException
     */
    private function getScore(TelegramChat $chat): void
    {
        $this->quizSender->sendUserScore($chat);
    }
}