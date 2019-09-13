<?php

use api\YandexApi;
use Unirest\Exception;
use bot\Bot;
use bot\Error;

require_once './vendor/autoload.php';
include './config/response_text.php';

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

                $bot->status();
                $bot->send($msg, $kbd);
            }

            //если команда "Проработать установку"
            if (strcasecmp($bot->getPayload(), CMD_INSTALLATION) === 0 || strcasecmp($bot->getText(), TEXT_INSTALLATION) === 0 || strcasecmp($bot->getPayload(), CMD_CLARIFY) === 0) {
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
                $inst = $bot->getText();
                $bot->logFile($inst);
                $trans = '1textchange1';
                $forSpeechText = str_replace($trans, str_replace(' ', ' - ', $inst), $text['res_to_inefficient_installation']);
                $regArr = ['- ', '+о'];
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
            if (strcasecmp($bot->getPayload(), CMD_FLIP) === 0 || strcasecmp($bot->getPayload(), CMD_CLARIFY_EFFECT) === 0) {
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
            }
            // no break
        case CALLBACK_API_EVENT_MESSAGE_REPLY:
            $bot->callbackOkResponse();

            // no break
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
    new Error();
}
