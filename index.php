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

            if (file_exists($bot->getUserId() . '.txt')) {
                $file = file_get_contents($bot->getUserId() . '.txt');
            }
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
                file_put_contents($bot->getUserId() . '.txt', 0);
                $bot->send($msg, $kbd);
            }

            //если команда "Проработать установку"
            if (strcasecmp($bot->getPayload(), CMD_INSTALLATION) === 0 || strcasecmp($bot->getText(), TEXT_INSTALLATION) === 0) {
                $msg = $text['inefficient_installation'];
                $kbd = [
                    'one_time' => true,
                    'buttons' => []
                ];
                file_put_contents($bot->getUserId() . '.txt', 1);
                $bot->send($msg, $kbd);
            }
            //обработка неэффективной установки
            if ($file === '1') {
                $resText = $text['res_to_inefficient_installation'];
                $inst = $bot->getText();
                $trans = '1textchange1';
                $msg = str_replace($trans, $inst, $resText);

                $kbd = [
                    'one_time' => false,
                    'buttons' => [
                        [
                            $bot->getBtn(TYPE_TEXT, 'Перевернуть установку', COLOR_POSITIVE, CMD_FLIP),
                        ]
                    ]
                ];
                file_put_contents($bot->getUserId() . '.txt', 0);
                $bot->send($msg, $kbd);
            }

            //обработка кнопки "Перевернуть установку"
            if (strcasecmp($bot->getPayload(), CMD_FLIP) === 0) {
                $msg = 'Вот тут не понятно, человек сам должен написать перевернутую установку или бот должен ее перевернуть?';

                $kbd = [
                    'one_time' => false,
                    'buttons' => [
                        [
                            $bot->getBtn(TYPE_TEXT, 'Следующая установка', COLOR_PRIMARY, CMD_INSTALLATION)
                        ],
                        [
                            $bot->getBtn(TYPE_TEXT, 'Вернуться в начало', COLOR_SECONDARY, CMD_START)
                        ]
                    ]
                ];
                file_put_contents($bot->getUserId() . '.txt', 2);
                $bot->send($msg, $kbd);
            }
        case CALLBACK_API_EVENT_MESSAGE_REPLY:
            $bot->callbackOkResponse();

        default:
            $bot->init();
            $msg = 'Я такой команды не знаю';
            $kbd = [
                'one_time' => false,
                'buttons' => [
                    [
                        $bot->getBtn(TYPE_TEXT, 'Вернуться в начало', COLOR_PRIMARY, CMD_START)
                    ]
                ]
            ];
    }
} catch (Exception $e) {
    myLog('Error' . $e->getCode() . ' ' . $e->getMessage());
}