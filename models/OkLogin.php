<?php


namespace models;

use core\CRUD;

class OkLogin
{
    public $UserInfo;

    public function __construct($params=null)
    {
        $config = require 'config/social_login.php';
        $ok = $config['ok'];
        if (isset($params) && ($params[0]['param'] === 'code'))
        {
            $ok['code'] = $params[0]['val'];

            $ok['Url']='https://api.odnoklassniki.ru/oauth/token.do';

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $ok['Url']);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($ok)));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            $result = curl_exec($curl);
            curl_close($curl);

            $tokenInfo = json_decode($result, true);
            if (isset($tokenInfo['access_token'])) {
                $sign = md5("application_key={$ok["public_key"]}format=jsonmethod=users.getCurrentUser" . md5("{$tokenInfo['access_token']}{$ok["client_secret"]}"));

                $params = array(
                    'method'          => 'users.getCurrentUser',
                    'access_token'    => $tokenInfo['access_token'],
                    'application_key' => $ok['public_key'],
                    'format'          => 'json',
                    'sig'             => $sign
                );
                $this->UserInfo = json_decode(file_get_contents('http://api.odnoklassniki.ru/fb.do' . '?' . urldecode(http_build_query($params))), true);

            }

        }
    }
    public function AddOkUser($UserInfo)
    {
        if(is_array($UserInfo)){
            $db = new CRUD('users');
            $db->GetInfo(array('email'),null,'=','https://ok.ru/profile/'.$UserInfo['uid'],1,0);
            if($db->TotalRows === 0) {// Если у нас уже этот пользователь есть
                $db->Add(array('email' => 'https://ok.ru/profile/'.$UserInfo['uid'], 'password' => 'OK_LOGINED', 'name' => $UserInfo['first_name'], 'confirm' => 1, 'photo' => $UserInfo['pic_3']));
            }
            $_SESSION['User'] = $UserInfo['first_name'];
            $_SESSION['success']='Добро пожаловать '.$UserInfo['first_name'].'!';
            $_SESSION['User_info'] =array('email' => '', 'password' => '', 'name' => $UserInfo['first_name'], 'confirm' => 1, 'photo' => $UserInfo['pic_3']) ;
        }
        else{
            $_SESSION['alert']='Мы не смогли Вас авторизовать через Одноклассники';
        }
    }
}