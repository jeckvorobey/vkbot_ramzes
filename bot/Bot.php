<?php

namespace bot;

use api\VKApi;
use Exception;


class Bot
{
    private $msg = '';  //текст сообщения
    private $data = []; //массив полученных данных от сервера
    private $type = ''; //тип входящего сообщения
    private $secret = ''; //секретный ключ пришедший от сервера;
    private $text = ''; //текс входящего сообщения
    private $payload = ''; //дополнительная информация о кнопке
    private $randomID; //Рандомный ID исходящего сообщения
    private $vkId;

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

    public function getVkId()
    {
        return $this->vkId;
    }

    public function getData($key)
    {
        return $this->data[$key];
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
        self::dbConnect();

        $body = $this->data['object'];

        if (!empty($body)) {
            $this->vkId = (abs($body['from_id']) ?? $body['peer_id']);
            $this->text = $body['text'] ?? '';
            $this->payload = $body['payload'] ?? '';
            if ($this->payload) {
                $this->payload = json_decode($this->payload, true);
            }
        }
    }

    private static function dbConnect()
    {
        $dbConfig = include __DIR__ . '/../config/dbConfig.php';

        Db::getInstance()->Connect($dbConfig['db_user'], $dbConfig['db_password'], $dbConfig['db_base']);
    }


    //отправка сообщения пользователю
    public function send($msg, $kbd = [
        'one_time' => false,
        'buttons' => []
    ],$vkId, $voice = '')
    {
        $this->randomID = mt_rand(20, 999999999);
        try {
            VKApi::init()->messages()->send(VK_API_ACCESS_TOKEN, [
                'peer_id' => $vkId,
                'random_id' => $this->randomID,
                'attachment' => $voice,
                'message' => $msg,
                'keyboard' => json_encode($kbd, JSON_UNESCAPED_UNICODE)
            ]);
        } catch (Exception $e) {
            new Error();
        }
        $this->callbackOkResponse();
    }

    /**
     * Сохранение в БД очереди задния на обработку установки
     *
     * @return string
     * @var $inst integer
     */
    public function saveInst($instType, $userId)
    {
        return Db::getInstance()->Query('INSERT INTO `instalation_tbl`(`user_id`, `inst_text`, `type_inst`, `status`) VALUES ( :user_id, :inst_text, :type_inst, :status)',
            [
                'user_id' => $userId,
                'inst_text' => $this->text,
                'type_inst' => $instType,
                'status' => 1,
            ]);
    }

    public function callbackOkResponse()
    {
        $this->callbackResponse('OK');
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
