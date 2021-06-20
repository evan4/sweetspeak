<?php


namespace models;


use core\Mail;
use core\SimpleImage;
use core\CRUD;

class UsersActions
{

    public function userOnline($name): bool
    {
        if(isset($_SESSION['User']) && !empty($_SESSION['User']) && $_SESSION['User'] === $name){
            return true;
        }

        return false;
    }

    public function userStanding($date)
    {
        $now = new \DateTime("today");
        $origin = new \DateTime($date);
        
        $interval = $origin->diff($now);
        
        $years = (int) $interval->y;
        $months = (int) $interval->m;
        
        $date_format = ' уже ';

        if($years){
            
            $last_num = ($years % 10);
      
            switch ($last_num ) {
              case 0:
                $date_format .= $years.' лет';
                break;
              case 1:
                $date_format.= $years.' год';
                break;
              case 2:
              case 3:
              case 4:
                $date_format .= $years.' года';
                break;
              default:
                $date_format .= $years.' лет';
                break;
            }
            
            if($months){
                $date_format .= ' и ';
            }
        }
        
        switch ($months) {
            case 0:
                $date_format ='менее месяца';
                break;
            case 1:
                $date_format .= $interval->format('%m месяц');
                break;
            case 2:
            case 3:
            case 4:
                $date_format .= $interval->format('%m месяца');
                break;
            default:
                $date_format .= $interval->format('%m месяцев');
                break;
        }
        
        return $date_format;
    }

    public static function findUser($params=null,$action=null) // поиск пользователя в БД и проверка его входа/ либо проверка уникальности почты
    {
        if(is_array($params)) // Если нам скормили нормальный массив данных
        {
            for($i = 0, $iMax = count($params); $i < $iMax; $i++) // Перебираем массив и ищем интересующий нас ключ
            {
                if($params[$i]['param'] === 'UserEmail') {
                    $mail=$params[$i]['val'];
                    $db = new CRUD('users');
                    $result = $db->GetInfo(['email'],null,'=',$params[$i]['val'],1,0);
                    $user = $result->Resulting;
                    break; // пользователь помещен в массив Прекращаем цикл
                }
            }

            if(isset($user[0]['email'])) // если такой пользователь найден, то проверяем его дальше
            {
                if($action==="CheckLogin") // проверяем персональный экшен для этого поиска
                {
                    for($i=0,$iMax=count($params); $i < $iMax; $i++) // перебираем массив переменных дл поиска пароля
                    {
                        if(($params[$i]['param']==='UserPassword')&&(password_verify($params[$i]['val'],$user[0]['password']))) //Если всё совпало
                        {
                            $pass=$user[0]['password'];
                           if($user[0]['confirm']=='1') // Если пользователь прошел подтверждение его почты
                               {
                                   $_SESSION['User'] = $user[0]['name']; // Записали в сессию как зовут пользователя
                                   $_SESSION['User_info'] = $user[0];
                                   return true;
                                   break; // возвращаем ТРУ
                               }
                           else // Если пользователь не подтвердил свой ящик
                               {
                                   $_SESSION['alert']='Вы не подтвердили свой E-mail. На всякий случай мы повторно отправили Вам письмо с подтверждением Пожалйуста проверьте почту';
                                   $name=$user[0]['name'];
                                   $mail_send = new Mail('noreply@sweetspeak.ru', 'maximus2020Free', 'ssl://smtp.yandex.ru', 465, "UTF-8"); // Создаём экземпляр класса
                                   $from = array(
                                       "SweetSpeak", // Имя отправителя
                                       "noreply@sweetspeak.ru" // почта отправителя
                                   );

                                    $to = $mail;
                                    $text="<h3>Здравствуйте ".$name."!</h3><p>Вы получили это письмо для подтверждения регистрации на сайте <strong>".$_SERVER['SERVER_NAME']."</strong> </p><p>Для активации Вашего аккаунта пройдите по ссылке: <a href='https://".$_SERVER['SERVER_NAME']."/Dver/activate?login=".$pass."&mail=".$mail."'>https://".$_SERVER['SERVER_NAME']."/Dver/activate?login=".$pass."&mail=".$mail."</a></p>";
                                    $mail_send->send($to, 'Подтверждение почтового ящика. Повторное письмо', $text, $from);

                                   return true;
                                   break; // возвращаем ТРУ
                               }
                           }
                        }
                    }
                }
            }
            if($action==="CheckUniq")// Если пользователь небыл найден то возможно мы просто проверяем его уникальность
            {
               return true ;
            }

        }

    public static function RegisterUser($params = null) // Регистрация пользователя
    {
        for($i=0,$iMax=count($params); $i < $iMax; $i++) // Собираем необходимые данные для регистрации Имя пользователя
        {
            if($params[$i]['param']==='UserName')
            {
                $user=$params[$i]['val']; break;
            }
        }
        for($i=0,$iMax=count($params); $i < $iMax; $i++) //  Собираем необходимые данные для регистрации Почта пользователя
        {
            if(($params[$i]['param']=== 'UserEmail') && (filter_var($params[$i]['val'], FILTER_VALIDATE_EMAIL) !== false)) // Если пота прошла валидацию
            {

                $mail=$params[$i]['val']; break;
            }
        }
        for($i=0,$iMax=count($params); $i < $iMax; $i++) //  Собираем необходимые данные для регистрации Пароль пользователя
        {
            if($params[$i]['param']==="UserPassword")
            {
                $pass=$params[$i]['val'];break;
            }
        }

        if(isset($user, $mail, $pass)) // проверяем готовность к регистрации И если всё есть то начинаем писать пользователя  Базу
        {
            $password=password_hash($pass, PASSWORD_DEFAULT);
            $db = new CRUD('users'); // Инициализируем работу с таблицей users
            if($db->Add(array('email'=>$mail,'password'=>$password,'name'=>$user))){ // Если запись добавлена то отправляем письмо

                $mail_send = new Mail('noreply@sweetspeak.ru', 'maximus2020Free', 'ssl://smtp.yandex.ru', 465, "UTF-8"); // Создаём экземпляр класса
                $from = array(
                    "SweetSpeak", // Имя отправителя
                    "noreply@sweetspeak.ru" // почта отправителя
                );

                $to = $mail;
                $text="<h3>Здравствуйте ".$user."!</h3><p>Вы получили это письмо для подтверждения регистрации на сайте <strong>".$_SERVER['SERVER_NAME']."</strong> </p><p>Для активации Вашего аккаунта пройдите по ссылке: <a href='https://".$_SERVER['SERVER_NAME']."/Dver/activate?login=".$password."&mail=".$mail."'>https://".$_SERVER['SERVER_NAME']."/Dver/activate?login=".$password."&mail=".$mail."</a></p>";
                $mail_send->send($to, 'Подтверждение почтового ящика', $text, $from);


            }
            else
            {

            }
        }

    }

    public static function TryActivate($params = null) // Попытка подтвердить E-mail пользователя
    {
            $db = new CRUD('users'); // Инициализируем БД
            $rows = $db->Update (array('confirm' => '1'), 'AND','=',array('password'=>$params[0]['val'],'email'=>$params[1]['val']));  // Возвращаем результат попытки обновления
            return $rows;
    }

    public static function Remind($params=null) // Восстановление пароля
    {
        $db = new CRUD('users'); // Инициализируем БД
        $user=$db->GetInfo(array('email'),null,'=',$params[0]['val'],1,0)->Resulting;


        if(isset($user[0]['id'])) // Если таковой нашелся
        {
            $new_user_pass=uniqid("",true);
            $new_pass=password_hash($new_user_pass,PASSWORD_DEFAULT);
            $db->Update(array('password'=>$new_pass) , null,'=',array('id'=>$user[0]['id']));
            $mail_send = new Mail($_SERVER['SERVER_NAME']); // Создаём экземпляр класса
            $mail_send->setFromName("Администрация сайта"); // Устанавливаем имя в обратном адресе
            $mail_send->send($user[0]['email'], "Восстановление доступа", "<h3>Здравствуйте ".$user[0]['name']."!</h3><p>В этом письме Ваш временный пароль к сайту <strong>".$_SERVER['SERVER_NAME']."</strong> </p><p>Вы сможете сменить его в личном кабинете </p><p>Для входа сейчас используйте этот пароль: <strong>".$new_user_pass."</strong></p>");
            $_SESSION['success']='Временный пароль был отправлен на Email: '.$params[0]['val'];
        }
        else{
            $_SESSION['alert']='К сожалению нам не удалось найти такой учетной записи. Возможно Вы допустили ошибку при указании почты / или указали не ту ';
        }
    }

    public static function Update($params=null) // Изменение Учетных данных пользователя
    {

        foreach ($params as $el)
        {
            if($el['param']==='UserName')  {  $User=$el['val']; }
            elseif ($el['param']==='UserEmail') { $Email=$el['val']; }
            elseif ($el['param']==='UserPassword') { $Password=$el['val']; }
            elseif ($el['param']==='UserId')  { $Target=$el['val'];  }
            elseif ($el['param']==='UserPhoto') { $Photo=$el['val']; }
        }
          if(!isset($Photo)){$Photo='no_photo';}
          if(!isset($Password)){$Password='stay_old_password_1';}
            $Target=($_SESSION['User_info']['id']);
            $db= new CRUD('users');
            $curent_user=$db->Getinfo(array('id'),null,'=',$Target,1,0)->Resulting;

            if($curent_user[0]['id']) // Если есть такой пользователь то можно проверять что-там у него поменялось
            {
                if($User!==$curent_user[0]['name'])   { $name_flag=true;     }
                if($Email!==$curent_user[0]['email']) { $mail_flag=true;     }
                if($Password!=='stay_old_password_1') { $password_flag=true; }
                if($Photo!=='no_photo')               { $photo_flag=true;    }
                $result=''; // Создаем пустой результат
                if(isset($name_flag))
                {
                    $db->Update(array('name'=>$User),null,'=',array('id'=>$Target));
                    $_SESSION['User_info']['name']=$User;
                    $_SESSION['User']=$User;
                    $result .= '_name_changed_';
                }
                if(isset($mail_flag))
                {
                    $db->Update(array('email'=>$Email,'confirm'=>false),null,'=',array('id'=>$Target));


                    $mail_send = new Mail('noreply@sweetspeak.ru', 'maximus2020Free', 'ssl://smtp.yandex.ru', 465, "UTF-8"); // Создаём экземпляр класса
                    $from = array(
                        "SweetSpeak", // Имя отправителя
                        "noreply@sweetspeak.ru" // почта отправителя
                    );

                    $to = $Email;
                    $text="<h3>Здравствуйте ".$User."!</h3><p>Вы получили это письмо для подтверждения изменения почтового ящика  на сайте <strong>".$_SERVER['SERVER_NAME']."</strong> </p><p>Для активации Вашего аккаунта пройдите по ссылке: <a href='https://".$_SERVER['SERVER_NAME']."/Dver/activate?login=".$curent_user['password']."&mail=".$Email."'>https://".$_SERVER['SERVER_NAME']."/Dver/activate?login=".$curent_user['password']."&mail=".$Email."</a></p>";
                    $mail_send->send($to, 'Подтверждение почтового ящика', $text, $from);


                    $result .= '_email_changed_';

                }
                if(isset($password_flag))
                {
                    $new_pass=password_hash($Password,PASSWORD_DEFAULT);
                    $db->Update(array('password'=>$new_pass),null,'=',array('id'=>$Target));
                    $result .= '_password_changed_';
                }
                if(isset($photo_flag))
                {
                    if(isset($curent_user['photo']))
                    {
                        unset($curent_user['photo']);
                    }
                    if(($Photo['error'] === 0) && $Photo['type'] == "image/png") {
                        $name=uniqid('', true).".png";
                        move_uploaded_file($Photo['tmp_name'], "content/avatars/".$name); // Переносим полученный файл
                        $new_photo=self::ava($name);
                    }
                    if(($Photo['error'] === 0) && $Photo['type'] == "image/jpeg") {
                        $name=uniqid('', true).".jpeg";
                        move_uploaded_file($Photo['tmp_name'], "content/avatars/".$name); // Переносим полученный файл
                        $new_photo=self::ava($name);
                    }
                    if(($Photo['error'] === 0) && $Photo['type'] == "image/gif") {
                        $name=uniqid('', true).".gif";
                        move_uploaded_file($Photo['tmp_name'], "content/avatars/".$name); // Переносим полученный файл
                        $new_photo=self::ava($name);
                    }

                    if(isset($new_photo)){
                        $_SESSION['User_info']['photo']=$new_photo;
                        $db->Update(array('photo'=>$new_photo),null,'=',array('id'=>$Target));
                        $result .= '_photo_changed_';
                    }
                }


        }
        return $result;
    }

    private static function ava($name)
    {
        $new_photo="content/avatars/".$name;
        $image = new SimpleImage();
        $image->load($new_photo);
        $image->resize(200, 200);
        $image->save('content/avatars/ava_'.$name);
        $new_photo="/content/avatars/ava_".$name;
        unlink('content/avatars/'.$name);
        return $new_photo;
    }
    public static function ChangeFormModal($params=null)
    {
        if($params[0]['val']==='Регистрация')
        {
            $result= require 'modals/JSON/register_form.php';
        }
        elseif($params[0]['val']==='Восстановление пароля')
        {
            $result= require 'modals/JSON/remind_form.php';
        }
        elseif($params[0]['val']==='Вход')
        {
            $result=require 'modals/JSON/login_form.php';
        }
        return $result;
    }
}