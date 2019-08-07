<?php

use Unirest\Exception;

require_once './vendor/autoload.php';

if (!isset($_REQUEST)) {
    exit;
}

$bot = bot\Bot;

if (strcmp($bot->getSecret(), VK_API_SECRET_KEY) !== 0) exit();

try {
    switch ($bot->getType()) {
        case CALLBACK_API_EVENT_CONFIRMATION:
            echo CALLBACK_API_CONFIRMATION_TOKEN;
            break;
        case CALLBACK_API_EVENT_MESSAGE_NEW;
            $bot->init();

            break;
        default:
    }
} catch (Exception $e) {
    myLog('Error' . $e->getCode() . ' ' . $e->getMessage());
}

/**
 * формируем клавиатуру
 */
$kbd = [
    'one_time' => false,
    'buttons' => [
        [
            getBtn(TYPE_TEXT, 'Показать мой ID', COLOR_SECONDARY, CMD_ID),
            getBtn(TYPE_TEXT, 'Еще...', COLOR_PRIMARY, CMD_NEXT)
        ],
        [
            getBtn(TYPE_TEXT, 'OK', COLOR_POSITIVE),
            getBtn(TYPE_TEXT, 'Отмена', COLOR_NEGATIVE)
        ]
    ]
];
/**
 * Обработка нового сообщения
 */
if ($type === 'message_new') {
    $message = $data['object'] ?? [];
    $userId = $message['from_id'] ?? $message['peer_id'];
    $body = $message['text'] ?? '';
    $payload = $message['payload'] ?? '';

    $msg = 'Привет я Бот. Тут должно быть приветственное сообщение!';

    if ($payload) {
        $payload = json_decode($payload, true);
    }
    /**
     * ответ на команду покажи мой ID
     */
    if ($payload === CMD_ID) {
        $msg = 'Ваш ID: ' . $userId;
    }
    /**
     * Обработка кнопки еще... с отсылкой новой клавиатуры 
     */
    if ($payload === CMD_NEXT) {
        $kbd = [
            'one_time' => false,
            'buttons' => [
                [
                    getBtn(TYPE_TEXT, 'Пришли тайпинг...', COLOR_PRIMARY, CMD_TYPING)
                ],
                [
                    getBtn(TYPE_TEXT, 'назад', COLOR_NEGATIVE)
                ]
            ]
        ];
        $msg = 'Тут описание какого-то меню, с новой раскладкой клавиатуры';
    }

    // myLog($json);
    try {
        if ($msg !== null)
            $response = $vk->messages()->send(VK_API_ACCESS_TOKEN, [
                'peer_id' => $userId,
                'random_id' => rand(0, 9999999999),
                'message' => $msg,
                'keyboard' => json_encode($kbd, JSON_UNESCAPED_UNICODE)
            ]);
    } catch (Exception $e) {
        myLog('Error' . $e->getCode() . ' ' . $e->getMessage());
    }
}
/**
 * Вывод полученной строки в терминал
 */
function myLog($str)
{
    file_put_contents("php://stdout", "$str\n");
}
echo "OK";