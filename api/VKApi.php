<?php

namespace api;

use CURLFile;
use VK\Client\Enums\VKLanguage;
use VK\Client\VKApiClient;
use bot\Error;
use VK\Exceptions\Api\VKApiMessagesDenySendException;
use VK\Exceptions\Api\VKApiSaveFileException;
use VK\Exceptions\VKApiException;
use VK\Exceptions\VKClientException;

class VKApi
{
    private static $vk = null;

    /*
    * Запрещаем копировать объект
    */
    private function __construct()
    {
    }

    private function __sleep()
    {
    }

    private function __wakeup()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return VKApiClient|null
     */
    public static function init()
    {
        if (self::$vk == null) {
            self::$vk = new VKApiClient(VK_API_VERSION, VKLanguage::RUSSIAN);
        }
        return self::$vk;
    }

    //Получет адрес сервера для загрузки аудиосообщения
    public function uploadServer()
    {
        try {
            $uploadUrl = self::init()->docs()->getMessagesUploadServer(
                VK_API_ACCESS_TOKEN,
                [
                    'type' => 'audio_message',
                    'peer_id' => $this->userId
                ]
            );
        } catch (VKApiMessagesDenySendException $e) {
            new Error();
        } catch (VKApiException $e) {
            new  Error();
        } catch (VKClientException $e) {
            new Error();
        }

        return $uploadUrl['upload_url'];
    }

    //загрузка аудиоответа на сервер VK
    public function setAudioVk($url, $audioFile)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: multipart/form-data;charset=utf-8']);

        if ($audioFile) {
            $file['file'] = new CURLFile($audioFile, mime_content_type($audioFile), pathinfo($audioFile)['basename']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
        }
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            new Error();
            die();
        }
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            new Error();
            die();
        }

        $res = json_decode($response, true);
        try {
            $data = self::init()->docs()->save(VK_API_ACCESS_TOKEN, [
                'file' => $res['file'],
            ]);
            return $data;
        } catch (VKApiSaveFileException $e) {
            new Error();
        } catch (VKApiException $e) {
            new Error();
        } catch (VKClientException $e) {
            new Error();
        }
    }


}
