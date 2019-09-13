<?php

namespace bot;

use CURLFile;
use Error;
use tidy;
use VK\Client\VKApiClient;
use VK\Client\Enums\VKLanguage;

class Bot
{
    public static $vk = ''; //обьект VK API
    private $msg = '';  //текст сообщения
    private $data = []; //массив полученных данных от сервера
    private $type = ''; //тип входящего сообщения
    private $secret = ''; //секретный ключ пришедший от сервера
    private $userId = ''; //ID пользователя
    private $firstName = ''; //Имя пользователя
    private $lastName = ''; //Фамилия пользователя
    private $text = ''; //текс входящего сообщения
    private $payload = ''; //дополнительная информация о кнопке
    private $randomID; //Рандомный ID исходящего сообщения

    public function __construct()
    {
        $this->data = json_decode(file_get_contents('php://input'), true);
        $this->type = $this->data['type'];
        $this->secret = $this->data['secret'];
        // $this->myLog($this->data);
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;
    }

    public function getMsg()
    {
        return $this->msg;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getData($key)
    {
        return $this->data[$key];
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    public function init()
    {
        self::$vk = new VKApiClient(VK_API_VERSION, VKLanguage::RUSSIAN);

        $body = $this->data['object'];

        if (!empty($body)) {
            $this->userId = abs($body['from_id']) ?? $body['peer_id'];
            $this->text = $body['text'] ?? '';
            $this->payload = $body['payload'] ?? '';
            if ($this->payload) {
                $this->payload = json_decode($this->payload, true);
            }
        }

        $this->getUser(); //получает данные пользователя
    }
    //получает данные пользователя
    public function getUser()
    {
        $user = self::$vk->users()->get(VK_API_ACCESS_TOKEN, [
            'user_ids' => $this->userId
        ]);

        if (!empty($user)) {
            $this->firstName = $user[0]['first_name'] ?? '';
            $this->lastName = $user[0]['last_name'] ?? '';
        }
    }

    //отправка сообщения пользователю
    public function send($msg, $kbd = [
        'one_time' => false,
        'buttons' => []
    ], $voice = '')
    {
        $this->randomID = mt_rand(20, 999999999);
        self::$vk->messages()->send(VK_API_ACCESS_TOKEN, [
            'peer_id' => $this->userId,
            'random_id' => $this->randomID,
            'attachment' => $voice,
            'message' => $msg,
            'keyboard' => json_encode($kbd, JSON_UNESCAPED_UNICODE)
        ]);
        $this->callbackOkResponse();
    }
    //Получет адрес сервера для загрузки аудиосообщения
    public function uploadServer()
    {
        $uploadUrl = self::$vk->docs()->getMessagesUploadServer(
            VK_API_ACCESS_TOKEN,
            [
                'type' => 'audio_message',
                'peer_id' => $this->userId
            ]
        );

        return $uploadUrl['upload_url'];
    }

    //загрузка аудиоответа на сервер VK
    public function setAudioVk($url, $audioFile)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: multipart/form-data;charset=utf-8']);

        if ($audioFile) {
            $file['file'] = new CURLFile($audioFile, mime_content_type($audioFile), pathinfo($audioFile)['basename']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
        }
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            new Error();
        }
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            new Error();
        } else {
            $res = json_decode($response, true);
            $data = self::$vk->docs()->save(VK_API_ACCESS_TOKEN, [
                'file' => $res['file'],
            ]);
            return $data;
        }
    }
    /**
     * сохранение статуса диалога
     * по умолчанию метод записывает файл со статусом 0
     * @string 'put' записывает файл
     * @string 'get' получает статус
     */
    public function status($method = 'put', $status = 0)
    {
        if ($method === 'get') {
            $status = (int) file_get_contents(STATUS_DIRECTORY . '/' . $this->userId . '.txt');
            return $status;
        }

        if ($method === 'put') {
            return file_put_contents(STATUS_DIRECTORY . '/' . $this->userId . '.txt', $status);
        }
    }


    public function callbackOkResponse()
    {
        self::callbackResponse('OK');
        exit();
    }

    public function callbackResponse($var)
    {
        echo $var;
        exit();
    }

    //делаем кнопку для клавиатуры
    public function getBtn($typeBtn, $label, $color, $payload = '')
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

    public function errLog()
    {
    }

    public function logFile($logText)
    {
        $text = "\n========================\n";
        $text .= date('Y-m-d H:i:s') . "\n";
        $text .= "Пользователь: " . $this->firstName . " " . $this->lastName . "\n";
        $text .= "текст сообщения: " . $logText;
        file_put_contents(LOG_DIRECTORY . '/log_' . $this->userId . '.txt', $text, FILE_APPEND);
    }

    public function myLog($str)
    {
        if (is_array($str)) {
            $str = json_encode($str, JSON_UNESCAPED_UNICODE);
        }
        file_put_contents("php://stdout", "$str\n");
    }
}
