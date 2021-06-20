<?php


namespace controllers;



use models\FacebookLogin;
use models\VkLogin;
use models\YandexLogin;
use models\OkLogin;
use models\GoogleLogin;

class SocialController
{
    public function vk($params=null)
    {
        $model = new VkLogin($params); // Общаемся с параметрами
        $model->AddVkUser($model->UserInfo['response'][0]); // Авторизируем пользователя на сайте
        header('Location: /'); // Отвправляем пользователя на главную страницу
    }

    public function yandex($params=null)
    {
        $model = new YandexLogin($params);
        $model->AddYandexUser($model->UserInfo);
        header('Location: /');
    }

    public function ok($params=null)
    {
        $model = new OkLogin($params);
        $model->AddOkUser($model->UserInfo);
        header('Location: /');
    }
    public function google($params=null)
    {

        $model = new GoogleLogin($params);
        $model->AddGoogleUser($model->UserInfo);
        header('Location: /');
    }
    public function facebook($params=null)
    {

        $model = new FacebookLogin($params);
        $model->AddFacebookUser($model->UserInfo);
        header('Location: /');
    }


    public function LoginFrom($provider_name)
    {
        $config=require 'config/social_login.php';
        $provider=$config[$provider_name[0]['val']];
        header('Location:  ' . $provider['Url'] . '?' . urldecode(http_build_query($provider)) );
    }



}