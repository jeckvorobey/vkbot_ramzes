<?php

namespace bot;

class Bot
{
    static $vk = null; //обьект VK API
    private $msg = '';  //текст сообщения
    private $data = []; //массив полученных данных от сервера
    private $type = ''; //тип входящего сообщения
    private $secret = ''; //секретный ключ пришедший от сервера
    private $userID = ''; //ID пользователя 
    private $text = ''; //текс входящего сообщения
    private $payload = ''; //дополнительная информация о кнопке

    public function __construct()
    {
        $json = file_get_contents('php://input');
        $this->data = json_decode($json, true);
        $this->type = $this->data['type'];
        $this->secret = $this->data['secret'];
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
        return $this->userID;
    }

    public function getText()
    {
        return $this->text;
    }

    public function init()
    {

        use VK\Client\VKApiClient;
        use VK\Client\Enums\VKLanguage;

        if ($this->vk === null) {
            $this->vk = new VKApiClient(VK_API_VERSION, VKLanguage::RUSSIAN);
        }

        $body = $this->data['object'];
        if (!empty($body)) {
            $this->userID = abs($body['from_id']) ?? $body['peer_id'];
            $this->text = $body['text'] ?? '';
            $this->payload = $body['payload'] ?? '';
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
}