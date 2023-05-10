<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\TelegramChat;
use App\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Track>
 *
 * @method Track|null find($id, $lockMode = null, $lockVersion = null)
 * @method Track|null findOneBy(array $criteria, array $orderBy = null)
 * @method Track[]    findAll()
 * @method Track[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Track::class);
    }

    public function createTrack(TelegramChat $chat, Quiz $quiz): void
    {
        $track = new Track();
        $track->setTelegramChat($chat);
        $track->setQuiz($quiz);
        $track->setStatus('incomplete');
        $track->setCreatedAt(new \DateTimeImmutable());

        $this->save($track, true);
    }

    public function save(Track $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Track $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getLastTrackByChat(int $chatId): Track
    {
        return $this->createQueryBuilder('track')
            ->andWhere('track.telegram_chat = :id')
            ->setParameter('id', $chatId)
            ->orderBy('track.created_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function setNewStatus(Track $track, string $string): void
    {
        $track->setStatus($string);

        $this->save($track, true);
    }

    public function getActiveChatsByHours()
    {
        return $this->createQueryBuilder('track')
            ->select('DATE_FORMAT(track.created_at, \'%Y-%m-%d %H:00\') as hour')
            ->addSelect('COUNT(DISTINCT track.telegram_chat) as count')
            ->groupBy('hour')
            ->getQuery()
            ->getArrayResult();
    }

    public function getCorrectAnswersRate()
    {
        return $this->createQueryBuilder('track')
            ->select('DATE_FORMAT(track.created_at, \'%Y-%m-%d %H:00\') as hour')
            ->addSelect('(COUNT(CASE WHEN track.status = \'correct\' THEN 1 ELSE :null END) / COUNT(\'all\')) * 100 AS percentage')
            ->setParameter('null', null)
            ->groupBy('hour')
            ->getQuery()
            ->getArrayResult();
    }

    public function getIncompleteQuizzesAmount()
    {
        return $this->createQueryBuilder('track')
            ->select('(COUNT(CASE WHEN track.status = \'incomplete\' THEN 1 ELSE :null END) / COUNT(\'all\')) * 100 AS amount')
            ->setParameter('null', null)
            ->getQuery()
            ->getArrayResult();
    }

    public function getAllQuizzes(): int
    {
        return $this->count([]);
    }
    public function getAllChats()
    {
        return $this->createQueryBuilder('track')
            ->select('COUNT(DISTINCT track.telegram_chat) as chats')
            ->getQuery()
            ->getResult();
    }

}
