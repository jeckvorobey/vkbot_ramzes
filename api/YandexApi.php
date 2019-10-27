<?php

namespace api;

use bot\Error;

class YandexApi
{
    public function getVoice($text)
    {

        $audioFile = AUDIO_DIRECTORY . '/voice_' . md5($text) . '.ogg';
        if (file_exists($audioFile)) {
            return $audioFile;
        }

        $query = http_build_query([
            'text' => urldecode($text),
            'lang' => YANDEX_LANG,
            'speed' => YNDEX_SPEED,
            'voice' => YANDEX_VOICE,
            'emotion' => YANDEX_EMOTION,
            'folderId' => YANDEX_ID_CATALOG,
        ]);

        $headers = ['Authorization: Api-Key ' . YANDEX_API_KEY];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, YANDEX_URL);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($query !== false) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            new Error();
        }
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            new Error();
        } else {
            file_put_contents( AUDIO_DIRECTORY . '/voice_' . md5($text) . '.ogg', $response);
            return $audioFile = AUDIO_DIRECTORY . '/voice_' . md5($text) . '.ogg';
        }
        curl_close($ch);
    }

}
