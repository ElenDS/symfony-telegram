<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use Longman\TelegramBot\Telegram;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LongmanClient
{
    private array $telegramCred;
    private array $mysqlCred;
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->telegramCred = $parameterBag->get('telegram');
        $this->mysqlCred = $parameterBag->get('mysql');
    }

    public function createApiObject(): Telegram
    {
        $telegram = new Telegram($this->getTelegramToken(), $this->getTelegramUsername());
        $telegram->enableMySql($this->getMysqlCredentials());
        return $telegram;
    }

    protected function getTelegramToken(){
        return $this->telegramCred['token'];
    }
    protected function getTelegramUsername(){
        return $this->telegramCred['username'];
    }
    protected function getMysqlCredentials(): array
    {
        return [
            'host'     => $this->mysqlCred['host'],
            'port'     => $this->mysqlCred['port'],
            'user'     => $this->mysqlCred['user'],
            'password' => $this->mysqlCred['password'],
            'database' => $this->mysqlCred['database'],
        ];
    }
}