<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\TrackRepository;

class ReportService
{
    public function __construct(protected TrackRepository $trackRepository)
    {
    }

    public function createReport(): array
    {
        return array_merge(
            $this->chatsByHours(),
            $this->correctAnswers(),
            $this->incompleteQuizzes(),
            $this->allQuizzes(),
            $this->allChats()
        );
    }

    public function chatsByHours(): array
    {
        $chats = $this->trackRepository->getActiveChatsByHours();
        $chatsIntoArray = [];
        foreach ($chats as $chat) {
            $chatsIntoArray[] = [$chat['hour'], $chat['count']];
        }
        array_unshift($chatsIntoArray, ['Active Chats By Hours', 'pcs.']);

        return $chatsIntoArray;
    }

    public function correctAnswers(): array
    {
        $answers = $this->trackRepository->getCorrectAnswersRate();
        $answersIntoArray = [];
        foreach ($answers as $answer) {
            $answersIntoArray[] = [$answer['hour'], $answer['percentage']];
        }
        array_unshift($answersIntoArray, ['Correct Answers', '%']);

        return $answersIntoArray;
    }

    public function incompleteQuizzes(): array
    {
        $quizzes = $this->trackRepository->getIncompleteQuizzesAmount();

        return [
            ['incomplete quizzes, %', $quizzes[0]['amount']]
        ];
    }

    public function allQuizzes(): array
    {
        $quizzes = $this->trackRepository->getAllQuizzes();

        return [
            ['all quizzes', $quizzes]
        ];
    }

    public function allChats(): array
    {
        $chats = $this->trackRepository->getAllChats();

        return [
            ['all chats', $chats[0]['chats']]
        ];
    }
}