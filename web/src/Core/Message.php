<?php

namespace App\Core;

class Message
{

    /**
     * @return array
     */
    public static function getMessage(): array
    {
        $messageText = '';
        $messageType = '';
        if (isset($_SESSION['message'])) {
            $messageText = $_SESSION['message']['messageText'] ?? '';
            $messageType = $_SESSION['message']['messageType'] ?? '';
            unset($_SESSION['message']);
        }

        return ['messageText' => $messageText, 'messageType' => $messageType];
    }

    /**
     * @param string $messageText
     * @param string $messageType
     *
     * @return void
     */
    public static function setMessage(string $messageText, string $messageType = 'info'): void
    {
        $_SESSION['message'] = ['messageText' => $messageText, 'messageType' => $messageType];
    }

}