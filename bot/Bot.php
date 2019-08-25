<?php

namespace bot;

use tidy;
use VK\Client\VKApiClient;
use VK\Client\Enums\VKLanguage;

class Bot
{
    static $vk = null; //обьект VK API
    private $msg = '';  //текст сообщения
    private $data = []; //массив полученных данных от сервера
    private $type = ''; //тип входящего сообщения
    private $secret = ''; //секретный ключ пришедший от сервера
    private $userId = ''; //ID пользователя 
    private $text = ''; //текс входящего сообщения
    private $payload = ''; //дополнительная информация о кнопке
    private $randomID; //Рандомный ID исходящего сообщения

    public function __construct()
    {

        $this->data = json_decode(file_get_contents('php://input'), true);
        $this->type = $this->data['type'];
        $this->secret = $this->data['secret'];
        $this->myLog($this->data);
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
        if (self::$vk === null) {
            self::$vk = new VKApiClient(VK_API_VERSION, VKLanguage::RUSSIAN);
        }

        $body = $this->data['object'];

        if (!empty($body)) {
            $this->userId = abs($body['from_id']) ?? $body['peer_id'];
            $this->text = $body['text'] ?? '';
            $this->payload = $body['payload'] ?? '';
            if ($this->payload) {
                $this->payload = json_decode($this->payload, true);
            }
        }

        //if ($this->randomID === $body['random_id']) exit();
    }
    //отправка сообщения пользователю
    public function send($msg, $kbd = [
        'one_time' => false,
        'buttons' => []
    ])
    {
        $this->randomID = rand(111111111, 999999999);
        self::$vk->messages()->send(VK_API_ACCESS_TOKEN, [
            'peer_id' => $this->userId,
            'random_id' => $this->randomID,
            'message' => $msg,
            'keyboard' => json_encode($kbd, JSON_UNESCAPED_UNICODE)
        ]);
        $this->callbackOkResponse();
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

    function myLog($str)
    {
        if (is_array($str)) {
            $str = json_encode($str, JSON_UNESCAPED_UNICODE);
        }
        file_put_contents("php://stdout", "$str\n");
    }
}
