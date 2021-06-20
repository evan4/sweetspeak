<?php


namespace models;


use core\CRUD;

class VkLogin
{
    public $UserInfo;
    /**
     * @return string
     *
     * Инит возвращает кнопку авторизации через соц сеть ВК;
     */



    public function __construct($params)
    {
        $config = require 'config/social_login.php';
        $vk = $config['vk'];
        if (isset($params) && ($params[0]['param'] === 'code')) {
            $vk['code'] = $params[0]['val'];
            $token = json_decode(file_get_contents('https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($vk))), true);
            if (isset($token['access_token'])) {
                $params = array(
                    'uids' => $token['user_id'],
                    'fields' => 'uid,first_name,last_name,screen_name,bdate,photo_200_orig,city',
                    'access_token' => $token['access_token'],
                    'v' => '5.120'
                );
                $this->UserInfo = json_decode(file_get_contents('https://api.vk.com/method/users.get' . '?' . urldecode(http_build_query($params))), true);

            }
        }

    }

    public function AddVkUser($UserInfo)
    {
       if(is_array($UserInfo)){
       $db = new CRUD('users');
       $db->GetInfo(array('email'),null,'=','https://vk.com/id'.$UserInfo['id'],1,0);
       if($db->TotalRows === 0) {// Если у нас уже этот пользователь есть
           $db->Add(array('email' => 'https://vk.com/id' . $UserInfo['id'], 'password' => 'VK_LOGINED', 'name' => $UserInfo['first_name'], 'confirm' => 1, 'photo' => $UserInfo['photo_200_orig']));
       }
        $_SESSION['User'] = $UserInfo['first_name'];
        $_SESSION['success']='Добро пожаловать '.$UserInfo['first_name'].'!';
        $_SESSION['User_info'] =array('email' => '', 'password' => '', 'name' => $UserInfo['first_name'], 'confirm' => 1, 'photo' => $UserInfo['photo_200_orig']) ;
       }
       else{
           $_SESSION['alert']='Мы не смогли Вас авторизовать через ВК';
       }
    }


}