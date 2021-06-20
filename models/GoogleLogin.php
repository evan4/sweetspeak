<?php


namespace models;


use core\CRUD;

class GoogleLogin
{
    public $UserInfo;
    public function __construct($params)
    {
        $config = require 'config/social_login.php';
        $google = $config['google'];
        if (isset($params) && ($params[0]['param'] === 'code'))
        {
            $google['code'] = $params[0]['val'];
            $google['grant_type'] = 'authorization_code';
            $google['Url']='https://accounts.google.com/o/oauth2/token';
            $google['client_secret']='9rep8MIdvK_LbqmdWB6AkR_M';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $google['Url']);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($google)));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            $result = curl_exec($curl);
            curl_close($curl);

            $tokenInfo = json_decode($result, true);

            if (isset($tokenInfo['access_token'])) {
                $params['access_token'] = $tokenInfo['access_token'];

                $Info = file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$tokenInfo['access_token']);
                $this->UserInfo=json_decode($Info,true);

            }

        }
    }

    public function AddGoogleUser($UserInfo)
    {
        if(is_array($UserInfo)){
            $db = new CRUD('users');
            $db->GetInfo(array('email'),null,'=',$UserInfo['email'],1,0);
            if($db->TotalRows === 0) {// Если у нас уже этот пользователь есть
                $db->Add(array('email' => $UserInfo['email'], 'password' => 'Google_LOGINED', 'name' => $UserInfo['given_name'], 'confirm' => 1, 'photo' => $UserInfo['picture']));
            }
            $_SESSION['User'] = $UserInfo['given_name'];
            $_SESSION['success']='Добро пожаловать '.$UserInfo['given_name'].'!';
            $_SESSION['User_info'] =array('email' => '', 'password' => '', 'name' => $UserInfo['given_name'], 'confirm' => 1, 'photo' => $UserInfo['picture']) ;
        }
        else{
            $_SESSION['alert']='Мы не смогли Вас авторизовать через Google';
        }
    }

}