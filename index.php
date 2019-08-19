<?php

use Unirest\Exception;
use bot\Bot;

require_once './vendor/autoload.php';
include './config/response_text.php';

if (!isset($_REQUEST)) {
    echo 'OK';
    exit;
}

$bot = new Bot;

if ($bot->getSecret() !== VK_API_SECRET_KEY) exit();

try {
    switch ($bot->getType()) {
        case CALLBACK_API_EVENT_CONFIRMATION:

            echo CALLBACK_API_CONFIRMATION_TOKEN;
            break;

        case CALLBACK_API_EVENT_MESSAGE_NEW;
            $bot->init();
            //Если команда "начать"
            if (strcasecmp($bot->getPayload(), CMD_START) === 0 || strcasecmp($bot->getText(), TEXT_START) === 0) {
                $msg = $text['welcome_messages'];
                $kbd = [
                    'one_time' => true,
                    'buttons' => [
                        [
                            $bot->getBtn(TYPE_TEXT, 'Проработать установку', COLOR_SECONDARY, CMD_INSTALLATION),
                        ]
                    ]
                ];
                $bot->send($msg, $kbd);
            }

            //если команда "Проработать установку"
            if (strcasecmp($bot->getPayload(), CMD_INSTALLATION) === 0 || strcasecmp($bot->getText(), TEXT_INSTALLATION) === 0) {
                $msg = $text['inefficient_installation'];
                $kbd = [
                    'one_time' => true,
                    'buttons' => []
                ];
                $bot->send($msg, $kbd);
            }

            if ($_SESSION['change'] === 1) {
                $msg = 'Обработка текста: ' . $bot->getText();
                $kbd = [
                    'one_time' => false,
                    'buttons' => []
                ];
                $bot->send($msg, $kbd);
            }

        default:
            $bot->init();
            $msg = 'Я такой команды не знаю';
            $kbd = [
                'one_time' => false,
                'buttons' => [
                    [
                        $bot->getBtn(TYPE_TEXT, 'Вернуться в начало', COLOR_PRIMARY, CMD_START),
                    ]
                ]
            ];
    }
} catch (Exception $e) {
    myLog('Error' . $e->getCode() . ' ' . $e->getMessage());
}