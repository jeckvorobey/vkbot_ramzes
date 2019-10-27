<?php


namespace bot;

require_once __DIR__ . '/../vendor/autoload.php';

class AutoTask
{
    private static $sendMsg;
    private $data;

    public function init()
    {
        self::$sendMsg = new SendMsg();

        $this->data = self::$sendMsg->init();

        if (!empty($this->data)) {
            self::$sendMsg->setData($this->data);
        } else {
            die();
        }
    }
}

(new AutoTask)->init();