<?php


require_once './vendor/autoload.php';

use VK\Client\VKApiClient;
use VK\Client\Enums\VKLanguage;

$json = file_get_contents('php://input');
$data = json_decode($json, true);
$type = $data['type'] ?? '';


/**
 * Вывод полученной строки в терминал
 */
function myLog($str)
{
    file_put_contents("php://stdout", "$str\n");
}

if ($type === 'confirmation') {
    echo CALLBACK_API_CONFIRMATION_TOKEN;
    exit;
}
/**
 * создание кнопки
 */
function getBtn($typeBtn, $label, $color, $payload = '')
{
    return [
        'action' => [
            'type' => $typeBtn,
            'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'label' => $label
        ],
        'color' => $color
    ];
}
/**
 * Объект API VK
 */
$vk = new VKApiClient(VK_API_VERSION, VKLanguage::RUSSIAN);
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
    /**
     * обработка кнопки Пришли тайпинг
     */
    if ($payload === CMD_TYPING) {
        try {
            $response = $vk->messages()->setActivity(VK_API_ACCESS_TOKEN, [
                'peer_id' => $userId,
                'type' => 'typing'
            ]);
            $msg = null;
        } catch (Exception $e) {
            myLog('Error' . $e->getCode() . ' ' . $e->getMessage());
        }
    }

    // myLog($json);
    try {
        if ($msg !== null)
            $response = $vk->messages()->send(VK_API_ACCESS_TOKEN, [
                'peer_id' => $userId,
                'random_id' => rand(00000000000000000, 9999999999999999),
                'message' => $msg,
                'keyboard' => json_encode($kbd, JSON_UNESCAPED_UNICODE)
            ]);
    } catch (Exception $e) {
        myLog('Error' . $e->getCode() . ' ' . $e->getMessage());
    }
}

echo "OK";