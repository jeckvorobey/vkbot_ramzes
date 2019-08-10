<?php

use Unirest\Exception;
use bot\Bot;

require_once './vendor/autoload.php';

if (!isset($_REQUEST)) exit;

$bot = new Bot;

//if (strcmp($bot->getSecret(), VK_API_SECRET_KEY) !== 0) exit();

try {
    switch ($bot->getType()) {
        case CALLBACK_API_EVENT_CONFIRMATION:
            echo CALLBACK_API_CONFIRMATION_TOKEN;
            break;
        case CALLBACK_API_EVENT_MESSAGE_NEW;
            $bot->init();
            if ($bot->getPayload() === CMD_START || $bot->getText() === TEXT_START) {
                $msg = WELCOME_MESSAGES;
                $kbd = [
                    'one_time' => false,
                    'buttons' => [
                        [
                            $bot->getBtn(TYPE_TEXT, 'Проработать установку', COLOR_SECONDARY,   CMD_INSTALLATION),
                        ]
                    ]
                ];
                $bot->send($msg, $kbd);
                $bot->callbackOkResponse();
            }

            if (TEXT_INSTALLATION || CMD_INSTALLATION) {
                $msg = 'Напиши свою негативную установку';
            }
            break;
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

/**
 * формируем клавиатуру
 */
//$kbd = [
//    'one_time' => false,
//    'buttons' => [
//        [
//            getBtn(TYPE_TEXT, 'Показать мой ID', COLOR_SECONDARY, CMD_ID),
//            getBtn(TYPE_TEXT, 'Еще...', COLOR_PRIMARY, CMD_NEXT)
//        ],
//        [
//            getBtn(TYPE_TEXT, 'OK', COLOR_POSITIVE),
//            getBtn(TYPE_TEXT, 'Отмена', COLOR_NEGATIVE)
//        ]
//    ]
//];