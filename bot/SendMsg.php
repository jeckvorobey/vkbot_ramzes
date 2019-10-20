<?php


namespace bot;

use bot\Db;

class SendMsg
{
    private $inst_id;
    private $user_id;
    private $ins;
    private $inst_text;
    private $type_inst;
    private $status;


    public function init()
    {
        return Db::getInstance()->Select('SELECT * FROM `instalation_tbl` WHERE status = 1 LIMIT 1', []);
    }

    public function getData($data){
        //записываем данные в переменные и запускаем процес отправки сообщения
}

    public function efectedInst($text)
    {

        /* $inst = $bot->getText();
         $trans = '1textchange1';
         $resText = $text['res_to_inefficient_installation'];


         if ($bot->getUserSex() === 1) {
             $preg = '/(.)л([\s|\.])/';
             $resText = preg_replace($preg, '\1ла\2', $resText);
         }

         $forSpeechText = str_replace($trans, str_replace(' ', ' - ', $inst), $resText);
         $regArr = ['- ', '+'];
         $msg = str_replace($regArr, '', $forSpeechText);
         //создание аудио ответа
         $file = $yandexApi->getVoice($forSpeechText);
         $url = $bot->uploadServer();
         $voice = $bot->setAudioVk($url, $file);
         $voice = 'doc' . $voice['audio_message']['owner_id'] . '_' . $voice['audio_message']['id'] . '_' .
             $voice['audio_message']['access_key'];
         $kbd = [
             'one_time' => false,
             'buttons' => [
                 [
                     $bot->getBtn(TYPE_TEXT, 'Перевернуть установку', COLOR_POSITIVE, CMD_FLIP)
                 ],
                 [
                     $bot->getBtn(TYPE_TEXT, 'Уточнить установку', COLOR_SECONDARY, CMD_CLARIFY)
                 ]
             ]
         ];
         $bot->status();
         $bot->send($msg, $kbd, $voice);
         break;*/
    }
}