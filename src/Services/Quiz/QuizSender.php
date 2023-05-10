<?php

declare(strict_types=1);

namespace App\Services\Quiz;

use App\Entity\TelegramChat;
use App\Services\MessageService;
use App\Services\Telegram\BotCommands;
use Longman\TelegramBot\Exception\TelegramException;

class QuizSender
{
    public function __construct(protected QuizManager $quizManager, protected MessageService $messageService)
    {
    }

    /**
     * @throws TelegramException
     */
    public function sendNewQuiz(string $type, TelegramChat $chat): void
    {
        $newQuiz = $this->quizManager->generateQuiz($type, $chat);
        $arrayOptions = $newQuiz->getWrongAnswers();
        $arrayOptions[] = $newQuiz->getCorrectAnswer();

        $this->messageService->sendMessage($newQuiz->getTask(), $chat->getTelegramId(), $arrayOptions);
    }

    /**
     * @throws TelegramException
     */
    public function replyToAnswer(TelegramChat $chat, string $answer): void
    {
        $replyMessage = $this->quizManager->checkAnswer($chat, $answer);

        $this->messageService->sendMessage($replyMessage, $chat->getTelegramId());
    }

    /**
     * @throws TelegramException
     */
    public function sendUserScore(TelegramChat $chat): void
    {
        $this->messageService->sendMessage(sprintf('Your score is %d', $chat->getTotalScore()), $chat->getTelegramId());
    }

    /**
     * @throws TelegramException
     */
    public function askType($telegramId): void
    {
        $this->messageService->sendMessage('Please select the type of quiz', $telegramId,
            [BotCommands::GET_MATH_QUIZ, BotCommands::GET_THEORETIC_QUIZ]);
    }
}