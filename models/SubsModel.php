<?php


namespace models;

use core\CRUD;
use core\Mail;

class SubsModel
{
    public static function Add($params = null){
        if(is_null($params)){return false;}
        else{
            $table=new CRUD('subscribers');
            $result= $table->GetInfo(array('email'),null,'=',$params[0]['val'],1,0,null)->Resulting;
            if(is_array($result[0])){
                return 'email exists';
            }
            else{
                $table->Add(array('mail'=>$params[0]['val']));
                $mail_send = new Mail('noreply@sweetspeak.ru', 'maximus2020Free', 'ssl://smtp.yandex.ru', 465, "UTF-8"); // Создаём экземпляр класса
                $from = array(
                    "SweetSpeak", // Имя отправителя
                    "noreply@sweetspeak.ru" // почта отправителя
                );

                $to = $params[0]['val'];
                $text="<h3>Здравствуйте!</h3><p>Вы получили это письмо так как была оформлена подписка на новости с сайта  <strong>".$_SERVER['SERVER_NAME']."</strong> </p>
                <p>Если вы не хотите получать оповещения пройдите по ссылке: <a href='https://".$_SERVER['SERVER_NAME']."/Subs/Delete?mail=".$to."'>https://".$_SERVER['SERVER_NAME']."/Subs/Delete?mail=".$to."</a></p>";
                $mail_send->send($to, 'Новости от SweetSpeak', $text, $from);


                $result .= '_email_changed_';
                return true;
            }
        }
    }

    public static function Delete($params = null){
        if (is_null($params)){return false;}
        else{
            $table=new CRUD('subscribers');
            $result = $table->GetInfo(array('mail'),null,'=',$params[0]['val'],1,0,null)->Resulting;
            if(is_array($result[0])){
                return false;
            }
            else{
                $table->Delete(array('mail'=>$params[0]['val']),null,'=');
                return true;
            }
        }
    }
}