<?php

namespace bot;


use api\Vk;
use api\YandexApi;

class SendMsg
{
    private static $bot = null;
    private static $yandexApi = null;
    private static $user = null;
    private $inst_id;
    private $user_id;
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
        self::dbConnect();
        $this->text = require_once __DIR__ . '/../config/response_text.php';
        return Db::getInstance()->Select('SELECT * FROM `instalation_tbl` WHERE `status` = 1 LIMIT 1');
    }

    public function setData($data)
    {
        $this->inst_id = $data[0]['inst_id'];
        $this->user_id = $data[0]['user_id'];
        $this->inst_text = $data[0]['inst_text'];
        $this->type_inst = $data[0]['type_inst'];
        $this->status = $data[0]['status'];
        $this->create_at = $data[0]['create_at'];
        $this->update_at = $data[0]['update_at'];



        self::user()->init($this->user_id);

        if (+$this->type_inst === 1) {
            $this->resText = $this->text['res_to_inefficient_installation'];
            $this->noEfectedInst();
        }

        if (+$this->type_inst === 2) {
            $this->resText = $this->text['res_to_effective_installation'];
            $this->EfectedInst();
        }

    }

    private static function dbConnect()
    {
        $dbConfig = include __DIR__ . '/../config/dbConfig.php';

        Db::getInstance()->Connect($dbConfig['db_user'], $dbConfig['db_password'], $dbConfig['db_base']);
    }


    private static function user()
    {
        if (self::$user == null) {

            self::$user = new User();
        }
        return self::$user;
    }

    private static function bot()
    {
        if (self::$bot == null) {
            self::$bot = new Bot();
        }
        return self::$bot;
    }

    private static function yandexApi()
    {
        if (self::$yandexApi == null) {
            self::$yandexApi = new YandexApi();
        }
        return self::$yandexApi;
    }

    private function noEfectedInst()
    {
        if (+self::user()->sex === 1) {
            $preg = '/(.)л([\s|\.])/';
            $this->resText = preg_replace($preg, '\1ла\2', $this->text['res_to_inefficient_installation']);
        }

        $forSpeechText = str_replace($this->trans, str_replace(' ', ' - ', $this->inst_text), $this->resText);
        $regArr = ['- ', '+'];
        $this->msg = str_replace($regArr, '', $forSpeechText);
        //создание аудио ответа
        $file = self::yandexApi()->getVoice($forSpeechText);
        $url = (new Vk)->uploadServer(self::user()->userVkId);
        $voice = (new Vk)->setAudioVk($url, $file);
        $voice = 'doc' . $voice['audio_message']['owner_id'] . '_' . $voice['audio_message']['id'] . '_' . $voice['audio_message']['access_key'];
        $kbd = [
            'one_time' => false,
            'buttons' => [
                [
                    self::bot()->getBtn(TYPE_TEXT, 'Перевернуть установку', COLOR_POSITIVE, CMD_FLIP)
                ],
                [
                    self::bot()->getBtn(TYPE_TEXT, 'Уточнить установку', COLOR_SECONDARY, CMD_CLARIFY)
                ],
                [
                    self::bot()->getBtn(TYPE_TEXT, 'В начало', COLOR_PRIMARY, CMD_START)
                ],
            ]
        ];
        self::bot()->upInstStatus($this->inst_id, 0);
        self::bot()->send($this->msg, $kbd, self::user()->userVkId, $voice);
    }

    private function EfectedInst()
    {
        $forSpeechText = str_replace($this->trans, str_replace(' ', ' - ', $this->inst_text), $this->text['res_to_effective_installation']);
        $this->msg = str_replace('- ', '', $forSpeechText);
        //создание аудио ответа
        $file = self::yandexApi()->getVoice($forSpeechText);
        $url = (new Vk)->uploadServer(self::user()->userVkId);
        $voice = (new Vk)->setAudioVk($url, $file);
        $voice = 'doc' . $voice['audio_message']['owner_id'] . '_' . $voice['audio_message']['id'] . '_' . $voice['audio_message']['access_key'];

        $kbd = [
            'one_time' => false,
            'buttons' => [
                [
                    self::bot()->getBtn(TYPE_TEXT, 'Следующая установка', COLOR_POSITIVE, CMD_INSTALLATION)
                ],
                [
                    self::bot()->getBtn(TYPE_TEXT, 'Уточнить установку', COLOR_SECONDARY, CMD_CLARIFY_EFFECT)
                ],
                [
                    self::bot()->getBtn(TYPE_TEXT, 'В начало', COLOR_PRIMARY, CMD_START)
                ]
            ]
        ];

        self::bot()->upInstStatus($this->inst_id, 0);
        self::bot()->send($this->msg, $kbd, self::user()->userVkId, $voice);
    }
}


