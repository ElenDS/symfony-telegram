<?php

declare(strict_types=1);

namespace App\Services;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class MessageService
{
    /**
     * @throws TelegramException
     */
    public function sendMessage(string $messageText, int $chatId, array $options = null): void
    {
        if (!$options) {
            Request::sendMessage([
                'chat_id' => $chatId,
                'text' => $messageText
            ]);
        } else {
            $options = $this->createQuizOptions($options);
            Request::sendMessage([
                'chat_id' => $chatId,
                'text' => $messageText,
                'reply_markup' => json_encode($options)
            ]);
        }
    }

    public function createQuizOptions(array $answersArray): array
    {
        if (count($answersArray) > 2) {
            shuffle($answersArray);

            return [
                'keyboard' => [
                    [
                        ['text' => $answersArray[0]],
                        ['text' => $answersArray[1]]
                    ],
                    [
                        ['text' => $answersArray[2]],
                        ['text' => $answersArray[3]]
                    ]
                ],
                'resize_keyboard' => true
            ];
        }

        return [
            'keyboard' => [
                [
                    [
                        'text' => $answersArray[0],
                    ]
                ],
                [
                    [
                        'text' => $answersArray[1],
                    ]
                ]
            ],
            'resize_keyboard' => true
        ];
    }

}
