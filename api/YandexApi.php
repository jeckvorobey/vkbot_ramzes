<?php

namespace api;

class YandexApi
{
    private $token = 'CggaATEVAgAAABKABHyv91anAF4Xc5sGvKq2BPwQbxD1J4LDN5EvrJuTa73piGLNH2xjWsuio3bGMsd_vWyMoYlykFD7WC2YfJi56CPV09NBfp7k5PDf-gwAasQZLLOewxRl-DJm--WWnpWckEmg6R3NCidFU2iZ7qJoWunHEqrplKu3gzbQSStr6tfwdQUCmk3s70MHTx9GHc8YP5GQdAPQMA1oTmIoH9TfMv3Z1PoqhkFETVQr6Xzjzl3CnhUhyEhgoBN9tditWZSnb4GzVaeg6Ql7GMViENJRFwr4Jx_n7-CqyenMgQjK62pXFRHYfAgr_bhT0yE0zyiiT_duC-oo5pt_fmdqJX4VSre0gW3YxlzabDJRMteh7eLXZa77fOjKAUHDO35jAnOfMkNOguz7WZ2hjIGBKPb8ENDm1-X7LavuQf_bmjzWCwyjpFWELwnw4Cgnv_69jGVYvayCBVkESLxmJUQp6uAsZeuMM8f01FJ9qzmtRSVb_uMIPDsnFfa2Mhiwr3oUVjCnP6QiGGJbPK15iWv-uB66WmgXZtt-RLuDKimSqNu-QBZD796-I3GibjMhyYDZnC12numJ4LRfCFYqXSXZkqDIFuQitlbLfbfopfsgcg5nvM8Si67eJ2HPEC_qj3BhVFYorjNemwzE5SGL364e_oKb5b28V7soVQkNvl-nSkmPcRgiGmUKIGE5MzYxMjA4NGZlZDQxYjBiZDRhM2I1ODI4ZGU4YzA4EIfDresFGMeUsOsFIiMKFGFqZWF0M2pqZGU0cWJsanRnNWVuEgtKZWNrVm9yb2JleVoAMAI4AUoIGgExFQIAAABQASD0BA';

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

        $headers = ['Authorization: Bearer ' . $this->token];

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