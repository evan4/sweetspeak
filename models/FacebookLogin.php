<?php


namespace models;


use core\CRUD;

class FacebookLogin
{
    public $UserInfo;
    public function __construct($params)
    {
        $config = require 'config/social_login.php';
        $facebook = $config['facebook'];
        if (isset($params) && ($params[0]['param'] === 'code')) {
            $facebook['code'] = $params[0]['val'];
            $facebook['Url'] = 'https://graph.facebook.com/oauth/access_token';
            $tokenInfo=json_decode(file_get_contents($facebook['Url'] . '?' . http_build_query($facebook)),true );

            if (count($tokenInfo) > 0 && isset($tokenInfo['access_token'])) {
                $params = array('access_token' => $tokenInfo['access_token']);

                $this->UserInfo = json_decode(file_get_contents('https://graph.facebook.com/me' . '?' . urldecode(http_build_query($params))), true);

            }

        }

    }
    public function AddFacebookUser($UserInfo)
    {
        if(is_array($UserInfo)){
            $db = new CRUD('users');
            $db->GetInfo(array('email'),null,'=','https://facebook.com?id='.$UserInfo['id'],1,0);
            if($db->TotalRows === 0) {// Если у нас уже этот пользователь есть
                $db->Add(array('email' => 'https://facebook.com?id=' . $UserInfo['id'], 'password' => 'Facebook_LOGINED', 'name' => $UserInfo['name'], 'confirm' => 1 ));
            }
            $_SESSION['User'] = $UserInfo['name'];
            $_SESSION['success']='Добро пожаловать '.$UserInfo['name'].'!';
            $_SESSION['User_info'] =array('email' => '', 'password' => '', 'name' => $UserInfo['name'], 'confirm' => 1 ) ;
        }
        else{
            $_SESSION['alert']='Мы не смогли Вас авторизовать через Facebook';
        }
    }
}