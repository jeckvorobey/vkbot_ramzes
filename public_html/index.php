<?php

use api\YandexApi;
use bot\Bot;
use bot\Error;
use bot\User;


require_once '../vendor/autoload.php';
include '../config/response_text.php';

if (!isset($_REQUEST)) {
    echo 'OK';
    exit;
}

new Error();

$bot = new Bot;


if ($bot->getSecret() !== VK_API_SECRET_KEY) {
    exit();
}

$yandexApi = new YandexApi;

try {
    switch ($bot->getType()) {
        case CALLBACK_API_EVENT_CONFIRMATION:

            echo CALLBACK_API_CONFIRMATION_TOKEN;
            break;

        case CALLBACK_API_EVENT_MESSAGE_REPLY:
            $bot->callbackOkResponse();
            break;

        case CALLBACK_API_EVENT_MESSAGE_NEW:
            $bot->init();
            $user = new User($bot->getVkId());
            //Если команда "Начать"
            if ($bot->getText() === TEXT_START || $bot->getPayload() === CMD_START) {
                $msg = $text['welcome_messages'];

                $kbd = [
                    'one_time' => true,
                    'buttons' => [
                        [
                            $bot->getBtn(TYPE_TEXT, 'Проработать установку', COLOR_SECONDARY, CMD_INSTALLATION),
                        ]
                    ]
                ];
                $bot->send($msg, $kbd, $user->userVkId);
            } //если команда "Проработать установку"
            elseif ($bot->getPayload() === CMD_INSTALLATION || $bot->getText() === TEXT_INSTALLATION || $bot->getPayload() === CMD_CLARIFY) {
                $msg = $text['inefficient_installation'];
                $kbd = [
                    'one_time' => true,
                    'buttons' => []
                ];
                $user->upStatusDialog(1);
                $bot->send($msg, $kbd, $user->userVkId);
            } //обработка неэффективной установки
            elseif (+$user->dialog === 1) {
                $bot->saveInst(1, $user->userId);
                $user->upStatusDialog(0);

                $kbd = [
                    'one_time' => false,
                    'buttons' => [
                        [
                            $bot->getBtn(TYPE_TEXT, 'Уточнить установку', COLOR_SECONDARY, CMD_CLARIFY)
                        ],
                        [
                            $bot->getBtn(TYPE_TEXT, 'Перевернуть установку', COLOR_POSITIVE, CMD_FLIP)
                        ],
                        [
                            $bot->getBtn(TYPE_TEXT, 'В начало', COLOR_PRIMARY, CMD_START)
                        ],
                    ]
                ];

                $bot->send('Установка обрабатывается...', $kbd, $user->userVkId);
            } //обработка кнопки "Перевернуть установку"
            elseif ($bot->getPayload() === CMD_FLIP || $bot->getPayload() === CMD_CLARIFY_EFFECT) {
                $msg = $text['effective_installation'];
                $kbd = [
                    'one_time' => true,
                    'buttons' => []
                ];
                $user->upStatusDialog(2);
                $bot->send($msg, $kbd, $user->userVkId);
            } //обработка эффективной установки
            elseif (+$user->dialog === 2) {
                $bot->saveInst(2, $user->userId);
                $user->upStatusDialog(0);
                $kbd = [
                    'one_time' => false,
                    'buttons' => [
                        [
                            $bot->getBtn(TYPE_TEXT, 'Уточнить установку', COLOR_SECONDARY, CMD_CLARIFY_EFFECT)
                        ],
                        [
                            $bot->getBtn(TYPE_TEXT, 'В начало', COLOR_PRIMARY, CMD_START)
                        ],
                    ]
                ];
                $bot->send('Установка обрабатывается...', $kbd, $user->userVkId);
                //обработка текста
                /* $inst = $bot->getText();
                 $bot->logFile($inst);
                 $trans = '1textchange1';
                 $forSpeechText = str_replace($trans, str_replace(' ', ' - ', $inst), $text['res_to_effective_installation']);
                 $msg = str_replace('- ', '', $forSpeechText);
                 //создание аудио ответа
                 $file = $yandexApi->getVoice($forSpeechText);
                 $url = $bot->uploadServer();
                 $voice = $bot->setAudioVk($url, $file);
                 $voice = 'doc' . $voice['audio_message']['owner_id'] . '_' . $voice['audio_message']['id'] . '_' . $voice['audio_message']['access_key'];

                 $kbd = [
                     'one_time' => false,
                     'buttons' => [
                         [
                             $bot->getBtn(TYPE_TEXT, 'Следующая установка', COLOR_POSITIVE, CMD_INSTALLATION)
                         ],
                         [
                             $bot->getBtn(TYPE_TEXT, 'Уточнить установку', COLOR_SECONDARY, CMD_CLARIFY_EFFECT)
                         ],
                         [
                             $bot->getBtn(TYPE_TEXT, 'Вернуться в начало', COLOR_PRIMARY, CMD_START)
                         ]
                     ]
                 ];
                 $bot->status();
                 $bot->send($msg, $kbd, $voice);
             } else {
                 $msg = '';
                 $kbd = [
                     'one_time' => false,
                     'buttons' => [
                         [
                             $bot->getBtn(TYPE_TEXT, 'В начало', COLOR_PRIMARY, CMD_START)
                         ],
                     ]
                 ];
                 //$bot->status();
                 $bot->send($msg);*/
            }
        default:
            $bot->callbackOkResponse();
    }
} catch (Exception $e) {
    new Error();
}