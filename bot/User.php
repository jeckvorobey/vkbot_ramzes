<?php


namespace bot;


use api\Vk;
use VK\Exceptions\VKApiException;
use VK\Exceptions\VKClientException;

class User
{
    public $userId;
    public $userVkId = 0;
    public $firstName;
    public $lastName;
    public $sex;
    public $dialog;

    public function init($userId)
    {
        $user = $this->getUser($userId);

        if ($user) {
            $this->userId = $user[0]['user_id'];
            $this->userVkId = $user[0]['user_vk_id'];
            $this->firstName = $user[0]['first_name'];
            $this->lastName = $user[0]['last_name'];
            $this->sex = $user[0]['user_sex'];
            $this->dialog = $user[0]['dialog_status'];

        }
    }

    public function initVk($vkId)
    {
        //self::dbConnect();

        if (!$this->getUserIdVk($vkId)) {
            $this->getUserVk($vkId);
            $this->setUser();
        }

        $user = $this->getUserIdVk($vkId);

        if ($user) {
            $this->userId = $user[0]['user_id'];
            $this->userVkId = $user[0]['user_vk_id'];
            $this->firstName = $user[0]['first_name'];
            $this->lastName = $user[0]['last_name'];
            $this->sex = $user[0]['user_sex'];
            $this->dialog = $user[0]['dialog_status'];

        }
    }

    private static function dbConnect()
    {
        $dbConfig = include __DIR__ . '/../config/dbConfig.php';

        Db::getInstance()->Connect($dbConfig['db_user'], $dbConfig['db_password'], $dbConfig['db_base']);
    }

    //получает данные пользователя из VK
    public function getUserVk($id)
    {
        try {
            $user = Vk::init()->users()->get(VK_API_ACCESS_TOKEN, [
                'user_ids' => $id,
                'fields' => 'sex'
            ]);
        } catch (VKApiException $e) {
            new Error();
        } catch (VKClientException $e) {
            new Error();
        }

        if (!empty($user)) {
            $this->userVkId = $user[0]['id'] ?? $id;
            $this->firstName = $user[0]['first_name'] ?? '';
            $this->lastName = $user[0]['last_name'] ?? '';
            $this->sex = $user[0]['sex'] ?? 0;
        }

    }

    public function getUser($userId)
    {
        return Db::getInstance()->Select('SELECT * FROM `users_tbl` WHERE `user_id` = :id', [
            'id' => $userId,
        ]);
    }

    public function getUserIdVk($id)
    {

        return Db::getInstance()->Select('SELECT * FROM `users_tbl` WHERE `user_vk_id` = :id', [
            'id' => $id,
        ]);

    }

    public function setUser()
    {
        return Db::getInstance()->Query('INSERT INTO `users_tbl`(`user_vk_id`, `first_name`, `last_name`, `user_sex`) VALUES (:user_vk_id, :first_name, :last_name, :user_sex)', [
            'user_vk_id' => $this->userVkId,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'user_sex' => $this->sex,
        ]);
    }

    public function upStatusDialog(int $status)
    {
        return Db::getInstance()->Query('UPDATE `users_tbl` SET `dialog_status`= :status WHERE `user_id` = :user_id', [
            'status' => $status,
            'user_id' => $this->userId,
        ]);
    }
}
