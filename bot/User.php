<?php


namespace bot;


use api\VKApi;
use VK\Exceptions\VKApiException;
use VK\Exceptions\VKClientException;

class User
{
    public $userId;
    public $userVkId;
    public $firstName;
    public $lastName;
    public $sex;

    public function init($id)
    {
        self::dbConnect();

        if (!$this->getUserIdVk($id)) {
            $this->getUserVk($id);
            $this->setUser();
        }

        $user = $this->getUserIdVk($id);

        if ($user) {
            /*   $this->userId = $user[0]['user_id'];
               $this->userVkId = $user[0]['user_vk_id'];
               $this->firstName = $user[0]['first_name'];
               $this->lastName = $user[0]['last_name'];
               $this->sex = $user[0]['user_sex'];*/

            echo $user;
        }

        return true;
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
            $user = VKApi::init()->users()->get(VK_API_ACCESS_TOKEN, [
                'user_ids' => $id,
                'fields' => 'sex'
            ]);
        } catch (VKApiException $e) {
            new Error();
        } catch (VKClientException $e) {
            new Error();
        }

        if (!empty($user)) {
            $this->userVkId = $id;
            $this->firstName = $user[0]['first_name'] ?? '';
            $this->lastName = $user[0]['last_name'] ?? '';
            $this->userSex = $user[0]['sex'] ?? 0;
        }
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


}

$t = new User();
$t->init(502583350);