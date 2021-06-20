<?php


namespace models;


use core\CRUD;

class YandexLogin
{
    public $UserInfo;

    public function __construct($params)
    {
        $config = require 'config/social_login.php';
        $yandex = $config['yandex'];
        if (isset($params) && ($params[0]['param'] === 'code'))
        {
            $yandex['code'] = $params[0]['val'];
            $yandex['grant_type'] = 'authorization_code';
            $yandex['Url']='https://oauth.yandex.ru/token';

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $yandex['Url']);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($yandex)));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            $result = curl_exec($curl);
            curl_close($curl);

            $tokenInfo = json_decode($result, true);
            if (isset($tokenInfo['access_token']))
            {
                $params = array(
                    'format'       => 'json',
                    'oauth_token'  => $tokenInfo['access_token']
                );
                $this->UserInfo = json_decode(file_get_contents('https://login.yandex.ru/info' . '?' . urldecode(http_build_query($params))), true);
            }

        }
     }

     public function AddYandexUser($UserInfo)
     {

         if(is_array($UserInfo)){
             $db = new CRUD('users');
             $db->GetInfo(array('email'),null,'=',$UserInfo['default_email'],1,0);
             if($db->TotalRows === 0) {// Если у нас уже этот пользователь есть
                 $db->Add(array('email' => $UserInfo['default_email'], 'password' => 'YANDEX_LOGINED', 'name' => $UserInfo['first_name'], 'confirm' => 1, 'photo' => 'https://avatars.yandex.net/get-yapic/'.$UserInfo['default_avatar_id'].'/islands-200'));
             }
             $_SESSION['User'] = $UserInfo['first_name'];
             $_SESSION['success']='Добро пожаловать '.$UserInfo['first_name'].'!';
             $_SESSION['User_info'] =array('email' => '', 'password' => '', 'name' => $UserInfo['first_name'], 'confirm' => 1, 'photo' => 'https://avatars.yandex.net/get-yapic/'.$UserInfo['default_avatar_id'].'/islands-200') ;
         }
         else{
             $_SESSION['alert']='Мы не смогли Вас авторизовать через Ядекс';
         }
     }



}