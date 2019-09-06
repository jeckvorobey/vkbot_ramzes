<?php

namespace api;

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
            'voice' => YANDEX_VOICE,
            'emotion' => YANDEX_EMOTION,
            'folderId' => YANDEX_ID_CATALOG,
        ]);

        $headers = ['Authorization: Api-Key ' . YANDEX_API_KEY];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, YANDEX_URL);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
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
            $this->myLog("Error: " . curl_error($ch));
        }
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            $decodedResponse = json_decode($response, true);
            $this->myLog("Error code: " . $decodedResponse["error_code"] . "\r\n");
            $this->myLog("Error message: " . $decodedResponse["error_message"] . "\r\n");
        } else {
            file_put_contents(AUDIO_DIRECTORY . '/voice_' . md5($text) . '.ogg', $response);
            return $audioFile = AUDIO_DIRECTORY . '/voice_' . md5($text) . '.ogg';
        }
        curl_close($ch);
    }

    function myLog($str)
    {
        if (is_array($str)) {
            $str = json_encode($str, JSON_UNESCAPED_UNICODE);
        }
        file_put_contents("php://stdout", "$str\n");
    }
}
