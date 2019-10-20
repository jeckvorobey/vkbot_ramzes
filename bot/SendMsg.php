<?php

namespace bot;


use api\YandexApi;

class SendMsg
{
    private static $bot = null;
    private static $yandexApi = null;
    private $inst_id;
    private $user_vk_id;
    private $inst_text;
    private $type_inst;
    private $status;
    private $create_at;
    private $update_at;
    private $text;
    private $resText;
    private $msg;
    private $trans = '1textchange1';


    public function init()
    {
        $this->text = require_once __DIR__ . '/../config/response_text.php';
        self::bot()->getConnect();
        return Db::getInstance()->Select('SELECT * FROM `instalation_tbl` WHERE status = 1 LIMIT 1');
    }

    public function setData($data)
    {
        $this->inst_id = $data[0]['inst_id'];
        $this->user_vk_id = $data[0]['user_vk_id'];
        $this->inst_text = $data[0]['inst_text'];
        $this->type_inst = $data[0]['type_inst'];
        $this->status = $data[0]['status'];
        $this->create_at = $data[0]['create_at'];
        $this->update_at = $data[0]['update_at'];

        if ($this->type_inst == 1) {
            $this->noEfectedInst();
        } else {
            //отправка эфективной
        }

    }

    private static function bot(){
        if(self::$bot == null){
            self::$bot = new Bot();
        }
        return self::$bot;
    }

    private static function yandexApi(){
        if(self::$yandexApi == null){
            self::$yandexApi = new YandexApi();
        }
        return self::$yandexApi;
    }

    public function noEfectedInst()
    {
        self::bot()->setUserId($this->user_vk_id);
        if (self::bot()->getUserSex()[0]['user_sex'] == 1) {
            $preg = '/(.)л([\s|\.])/';
            $this->resText = preg_replace($preg, '\1ла\2', $this->text['res_to_inefficient_installation']);
        }

        $forSpeechText = str_replace($this->trans, str_replace(' ', ' - ', $this->inst_text), $this->text['res_to_inefficient_installation']);
        $regArr = ['- ', '+'];
        $this->msg = str_replace($regArr, '', $forSpeechText);
        //создание аудио ответа
       $file = self::yandexApi()->getVoice($forSpeechText);
        $url = self::bot()->uploadServer();
        $voice = self::bot()->setAudioVk($url, $file);
        $voice = 'doc' . $voice['audio_message']['owner_id'] . '_' . $voice['audio_message']['id'] . '_' .
            $voice['audio_message']['access_key'];
        $kbd = [
            'one_time' => false,
            'buttons' => [
                [
                    self::$bot->getBtn(TYPE_TEXT, 'Перевернуть установку', COLOR_POSITIVE, CMD_FLIP)
                ],
                [
                    self::$bot->getBtn(TYPE_TEXT, 'Уточнить установку', COLOR_SECONDARY, CMD_CLARIFY)
                ]
            ]
        ];
        self::bot()->setStatus(0);
        //self::$bot->send($this->msg, $kbd, $voice, $this->user_vk_id);
        $kbd = json_encode($kbd, true);
        echo "Сообщение: ". $this->msg . "\nКлава: " . $kbd . "\nГолос: " . $voice . "\nИД: " .
            $this->user_vk_id
    ."\n";
    }
}


