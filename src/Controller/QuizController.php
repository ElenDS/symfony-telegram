<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class QuizController extends AbstractController
{
    public function postQuiz(Request $request, QuizRepository $quizRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $quizData = [
            'task'=>$data['task'],
            'correct_answer' => $data['correct_answer'],
            'incorrect_answers' => $data['incorrect_answers'],
            'type' => $data['type']
        ];
        $quizRepository->createQuiz($quizData);

        return new JsonResponse($quizData);
    }

}
