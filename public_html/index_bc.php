<?php

use api\YandexApi;
use bot\Bot;
use bot\Error;

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

        case CALLBACK_API_EVENT_MESSAGE_NEW:
            $bot->init();

            if (file_exists(STATUS_DIRECTORY . '/' . $bot->getUserId() . '.txt')) {
                $status = $bot->status('get');
            }
            //Если команда "Начать"
            if ($bot->getPayload() === CMD_START || $bot->getText() === TEXT_START) {
                $msg = $text['welcome_messages'];

                $kbd = [
                    'one_time' => true,
                    'buttons' => [
                        [
                            $bot->getBtn(TYPE_TEXT, 'Проработать установку', COLOR_SECONDARY, CMD_INSTALLATION),
                        ]
                    ]
                ];

                $bot->status();
                $bot->send($msg, $kbd);
            }

            //если команда "Проработать установку"
            if ($bot->getPayload() === CMD_INSTALLATION || $bot->getText() === TEXT_INSTALLATION || $bot->getPayload() === CMD_CLARIFY) {
                $msg = $text['inefficient_installation'];
                $kbd = [
                    'one_time' => true,
                    'buttons' => []
                ];
                $bot->status('put', 1);
                $bot->send($msg, $kbd);
            }
            //обработка неэффективной установки
            if ($status === 1) {
                //обработка текста
                echo 'OK';
                $inst = $bot->getText();
                $bot->logFile($inst);
                $trans = '1textchange1';
                $resText = $text['res_to_inefficient_installation'];


                if ($bot->getUserSex() === 1) {
                    $preg = '/(.)л([\s|\.])/';
                    $resText = preg_replace($preg, '\1ла\2', $resText);
                }

                $forSpeechText = str_replace($trans, str_replace(' ', ' - ', $inst), $resText);
                $regArr = ['- ', '+'];
                $msg = str_replace($regArr, '', $forSpeechText);
                //создание аудио ответа
                $file = $yandexApi->getVoice($forSpeechText);
                $url = $bot->uploadServer();
                $voice = $bot->setAudioVk($url, $file);
                $voice = 'doc' . $voice['audio_message']['owner_id'] . '_' . $voice['audio_message']['id'] . '_' . $voice['audio_message']['access_key'];
                $kbd = [
                    'one_time' => false,
                    'buttons' => [
                        [
                            $bot->getBtn(TYPE_TEXT, 'Перевернуть установку', COLOR_POSITIVE, CMD_FLIP)
                        ],
                        [
                            $bot->getBtn(TYPE_TEXT, 'Уточнить установку', COLOR_SECONDARY, CMD_CLARIFY)
                        ]
                    ]
                ];
                $bot->status();
                $bot->send($msg, $kbd, $voice);
            }

            //обработка кнопки "Перевернуть установку"
            if ($bot->getPayload() === CMD_FLIP || $bot->getPayload() === CMD_CLARIFY_EFFECT) {
                $msg = $text['effective_installation'];
                $bot->status('put', 2);
                $bot->send($msg);
            }

            //обработка эффективной установки
            if ($status === 2) {
                //обработка текста
                $inst = $bot->getText();
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
                exit();
            }

            break;
        case CALLBACK_API_EVENT_MESSAGE_REPLY:
            $bot->callbackOkResponse();
            break;
        default:
            $msg = $text['incorrect_text'];
            $kbd = [
                'one_time' => false,
                'buttons' =>
                    [
                        $bot->getBtn(TYPE_TEXT, 'В начало', COLOR_PRIMARY, CMD_START)
                    ],
            ];
            $bot->status();
            $bot->send($msg);
    }
} catch (Exception $e) {
    new Error();
}
