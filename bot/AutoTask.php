<?php


namespace bot;

use bot\SendMsg;
use mysql_xdevapi\Exception;


class AutoTask
{
    private $sendMsg;
    private $data;

    public function init()
    {
       $this->sendMsg = new SendMsg;
        var_dump($this->sendMsg);
                //echo '\[ERROR\] '. $e->getMessage() . ' File: ' . $e->getFile() . 'Line: ' .$e->getLine();


     //   $this->data = self::$sendMsg->init();

        if(!empty($this->data)){
            var_dump($this->data);
        }else{
            echo 'Пусто';
        }
    }
}

$task = new AutoTask;
$task->init();