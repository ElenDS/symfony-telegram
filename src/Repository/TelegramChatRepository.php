<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TelegramChat;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TelegramChat>
 *
 * @method TelegramChat|null find($id, $lockMode = null, $lockVersion = null)
 * @method TelegramChat|null findOneBy(array $criteria, array $orderBy = null)
 * @method TelegramChat[]    findAll()
 * @method TelegramChat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TelegramChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TelegramChat::class);
    }
    public function createTelegramChat(int $telegramId): TelegramChat
    {
        $telegramChat = new TelegramChat();
        $telegramChat->setTelegramId($telegramId);
        $telegramChat->setTotalScore(0);
        $telegramChat->setCreatedAt(new DateTimeImmutable());

        $this->save($telegramChat, true);

        return $telegramChat;
    }

    public function save(TelegramChat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TelegramChat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function addToScore(TelegramChat $chat, int $points)
    {
        $score = $chat->getTotalScore() + $points;
        $chat->setTotalScore($score);

        $this->save($chat, true);
    }
}
