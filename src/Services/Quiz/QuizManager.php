<?php

declare(strict_types=1);

namespace App\Services\Quiz;

use App\Entity\Quiz;
use App\Entity\TelegramChat;
use App\Repository\QuizRepository;
use App\Repository\TelegramChatRepository;
use App\Repository\TrackRepository;

class QuizManager
{
    public function __construct(
        protected QuizRepository $quizRepository,
        protected TrackRepository $trackRepository,
        protected TelegramChatRepository $chatRepository
    ) {
    }

    public function generateQuiz(string $type, TelegramChat $chat): Quiz
    {
        $quiz = $this->quizRepository->getRandomQuiz($type);

        $this->trackRepository->createTrack($chat, $quiz);

        return $quiz;
    }

    public function checkAnswer(TelegramChat $chat, string $answer): string
    {
        $track = $this->trackRepository->getLastTrackByChat($chat->getId());
        $quiz = $track->getQuiz();

        if ($answer === $quiz->getCorrectAnswer()) {
            $points = QuizPoints::CORRECT;
            $replyMessage = sprintf('Correct answer! get %d points!', $points);

            $this->trackRepository->setNewStatus($track, 'correct');
        } else {
            $points = QuizPoints::INCORRECT;
            $replyMessage = sprintf('The answer is wrong! %d points!', $points);

            $this->trackRepository->setNewStatus($track, 'incorrect');
        }

        $this->setChatScore($chat, $points);

        return $replyMessage;
    }

    private function setChatScore(TelegramChat $chat, int $points): void
    {
        $this->chatRepository->addToScore($chat, $points);
    }
}