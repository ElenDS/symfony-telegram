<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quiz>
 *
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function createQuiz(array $quizData): Quiz
    {
        $quiz = new Quiz();
        $quiz->setTask($quizData['task']);
        $quiz->setCorrectAnswer($quizData['correct_answer']);
        $quiz->setWrongAnswers($quizData['incorrect_answers']);
        $quiz->setType($quizData['type']);
        $quiz->setCreatedAt(new \DateTimeImmutable());

        $this->save($quiz, true);

        return $quiz;
    }

    public function save(Quiz $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Quiz $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getRandomQuiz(string $type): ?Quiz
    {
        $randomId = rand(0, 11);

        return $this->createQueryBuilder('quiz')
            ->andWhere('quiz.type = :type')
            ->setParameter('type', $type)
            ->andWhere('quiz.id > :id')
            ->setParameter('id', $randomId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
