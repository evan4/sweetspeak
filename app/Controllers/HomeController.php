<?php

namespace App\Controllers;

use Mycms\Controller;
use Instagram\Api;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Models\User;
use App\Models\UserPhoto;
use App\Models\Article;
use App\Models\Complaint;
use App\Models\Subscriber;
use App\Models\Friend;

class HomeController extends Controller
{

    public function index()
    {
        if($this->session->has('User_info') && !empty($this->session->get('User_info'))){
            
            echo $this->twig->render('home/index.twig');
        }else {

            echo $this->twig->render('home/guest.twig');
        }

    }

    public function auth()
    {
        if(!$this->checkAjax()) redirect('/');

        $validation = $this->validation(filter_input_array(INPUT_POST));
        
        if( $validation['errors'] ){
            echo json_encode($validation['errors']);
            die();
        }

        $user = new User();
        
        $res = $user->getUser(
            ['users.id','users.name','users.email', 'users.password','users.confirm', 'users.status'],
            [ 'users.email' => $validation['data']['email'] ]
        );
       
        $error = [];
        
        if($res){
            
            if(password_verify( $validation['data']['password'], $res['password'] )){

                if((int) $res['confirm'] === 2){
                    $error['password'] = 'Ваша учетная запись заблокирована';
                }else{
                
                    unset($res['password']);
                    $_SESSION['User'] = $res['name'];
                    $_SESSION['User_info'] = $res;
                    echo json_encode(['success' => true]);
                    die();
                }
                
            }else{
                $error['password'] = 'Неверные логин или пароль';
            }
            
        }else{
            $error['password'] = 'Неверные логин или пароль';
        }
        
        echo json_encode($error);
        die();
        
    }

    /**
     * register new user via ajax
     *
     */
    public function singup()
    {
        if(!$this->checkAjax()) redirect('/');
        
        if(!isset($_POST['check'])){
            echo json_encode(['errors' => 'Прочтите условия']);
            die(); 
        }
        
        $params = filter_input_array(INPUT_POST);

        unset($params['check']);

        $validation = $this->validation($params);
        
        $user = new User();

        $result = $user->getUser(
            ['users.name','users.email', 'users.password'],
            [ 'users.email' => $validation['data']['email'] ]
        );

        if($result){
            $validation['errors']['email_unique'] = 'Такой email занят';
        }

        if( $validation['errors'] ){
            echo json_encode($validation['errors']);
            die();
        }

        $validation['data']['password'] = password_hash( 
            $validation['data']['password'],  
            PASSWORD_DEFAULT
        );
        
        $url = $_ENV['DOMAIN_NAME'].'/verifyEmail?signature=';
        $strtotime = strtotime("now");
        $uniqid = $strtotime . uniqid();
        $uniqid = uniqid($strtotime);
        $url .= $uniqid;

        $data = [
            'users.name' => $validation['data']['name'],
            'users.email' => $validation['data']['email'],
            'users.password' => $validation['data']['password'],
            'users.remember_token' => password_hash( 
                $uniqid,  
                PASSWORD_DEFAULT
            )
        ];
       
        $res = $user->saveUser($data);
        
        $error = [];

        if($res['success']){

            $res = $user->getUser(
                ['users.id','users.name','users.email', 'users.password','users.confirm', 'users.status'],
                [ 'users.email' => $validation['data']['email'] ]
            );

            unset($res['password']);
            $_SESSION['User'] = $res['name'];
            $_SESSION['User_info'] = $res;

            $url .= '&id='.$res['id'];
          
            $msg = $this->twig->render('emails/register.twig', [
                'url' => $url
            ]);
        
            $this->sendMail([
                'theme' => 'Подтвердить email адрес',
                'email' => $validation['data']['email'],
                'name' => $validation['data']['name'],
                'msg' => $msg 
            ]);

            echo json_encode(['success' => true]);
            die();

        }else {
            $error['registration'] = 'Произошла ошибка.';
        }

        echo json_encode($error);
        die();
    }

    public function recoveryPassword()
    {
        $params = filter_input_array(INPUT_POST);

        $validation = $this->validation($params);
        
        $user = new User();

        $result = $user->getUser(
            ['users.id', 'users.name','users.email'],
            [ 'users.email' => $validation['data']['email'] ]
        );
        
        if(!$result){
            $validation['errors']['email_unique'] = 'Такой email не существует';
        }

        if( $validation['errors'] ){
            echo json_encode($validation['errors']);
            die();
        }

        $url = $_ENV['DOMAIN_NAME'].'/recoveryPass?signature=';
        $strtotime = strtotime("now");
        $uniqid = $strtotime . uniqid();
        $uniqid = uniqid($strtotime);
        $url .= $uniqid;
        
        $data = [
            'id' => $result['id'],
            'remember_token' => password_hash( 
                $uniqid,  
                PASSWORD_DEFAULT
            ),
            'verify_token_timestamp' => date('Y-m-d H:i:s',$strtotime)
        ];
        
        $res = $user->updateUser($data);
        
        $error = [];

        if($res){
            $url .= '&id='.$result['id'];
          
            $msg = $this->twig->render('emails/recovery_password.twig', [
                'url' => $url
            ]);
            
            $this->sendMail([
                'theme' => 'Смена пароля',
                'email' => $result['email'],
                'name' => $result['name'],
                'msg' => $msg 
            ]);

            echo json_encode(['success' => true]);
            die();

        }else {
            $error['registration'] = 'Произошла ошибка.';
        }

        echo json_encode($error);
        die();

    }

    public function proposal()
    {
        $result['success'] = false;
        $args = array(
            'name' => [
                'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags' => FILTER_NULL_ON_FAILURE, 
            ],
            'email'  => [
                'filter' => FILTER_SANITIZE_EMAIL,
                'flags' => FILTER_NULL_ON_FAILURE, 
            ],
            'tel'  => [
                'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags' => FILTER_NULL_ON_FAILURE, 
                'options'  => array('min_range' => 1, 'max_range' => 15)
            ],
            'text'  => [
                'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags' => FILTER_NULL_ON_FAILURE, 
            ],
        );
        
        $params = filter_input_array(INPUT_POST, $args, true);
        
        if(isset($params['name']) && isset($params['email'])
            && isset($params['tel']) && isset($params['text'])){
                
                $msg = $this->twig->render('emails/feedback.twig', [
                    'theme' => 'Форма обратной связи',
                    'name' => $params['name'],
                    'email' => $params['email'],
                    'tel' => $params['tel'],
                    'msg' => $params['text'],
                ]);
                
                $this->sendMail([
                    'theme' => 'Форма обратной связи',
                    'email' => $_ENV['ADMIN_EMAIL'],
                    'name' => 'admin',
                    'msg' => $msg
                ]);
    
                echo json_encode(['success' => true]);
                die();

        }

        echo json_encode(['res'=> $result]);
        die();
    }

    public function getcurrentUser()
    {
        $email = $this->checkEmail($_POST['email']);
        if($email){
            if($_SESSION['User_info']['email'] == $email){
                echo json_encode([
                    'user' => $_SESSION['User_info']
                ]);
                die();
            }
            echo json_encode(['error' => 'Пользователь не существует']);
            die();
        }
        echo json_encode(['error' => 'Пользователь не существует']);
        die();
    }

    public function getcurrentUserInfo()
    {
        $email = $this->checkEmail($_POST['email']);
        
        if($email){
            $user = new User();

            if($_SESSION['User_info']['email'] == $email){
                $id  = (int) $_SESSION['User_info']['id'];
                $res = $user->getUser(
                    ['users.id','users.name','users.nickname','users.confirm',
                    'users.balance','users.photo','users.avatar','users.author_points',
                    'users.created_at','users.userstatus','users.motto',
                    'users.bio','users.gender','users.age','users.city','users.target',
                    'users.instagram','users.vk','users.twitter','users.facebook',
                    'users.website', 'users.email','users.status',              
                    ],
                    [ 
                        'users.email' => $email,
                        'join' => " LEFT JOIN `materials` ON materials.author_id = users.id LEFT JOIN `comments` ON comments.author_id = users.id",
                        'sum' => ", sum(case when materials.author_id = ".$id ." then 1 else 0 end) as articles,
                              sum(case when comments.author_id = ".$id ." then 1 else 0 end) as comments",
                    ]
                );
                
                $userPhotos = new UserPhoto();

                $res['photos'] = $userPhotos->getUserPhotos(
                ['user_photos.id', 'user_photos.photo'],
                ['user_photos.author_id' => $res['id']]);
                            
                echo json_encode(["user" => $res] );
                die();
            }
        }
        echo json_encode(['error' => 'Пользователь не существует']);
        die();
    }

    public function instagram()
    {
        $cachePool = new FilesystemAdapter('Instagram', 0, CACHE );
        
        $api = new Api($cachePool);
        $api->login($_ENV['INSTAGRAM_LOGIN'], $_ENV['INSTAGRAM_PASSWORD']); 
        
        $profile = $api->getProfile($_ENV['INSTAGRAM_USERNAME']);
        
        $res = [
            'followers' => $profile->getFollowers(),
            'total' => $profile->getMediaCount(),
            'url' => 'https://www.instagram.com/'.$_ENV['INSTAGRAM_USERNAME'].'/',
            'photos' => []
        ];
      
        $medias = $profile->getMedias();
        if($res['total'] > 8){
            $medias = array_slice($medias, 0, 8);
        }
        
        foreach($medias as $image){
          
            $res['photos'][] =[
                'image' => $image->getThumbnailSrc(),
                'caption' => $image->getCaption(),
                'url' =>'https://www.instagram.com/p/'.$image->getShortCode()
            ];
        }
        echo json_encode($res);
        die();
    }

    public function changeTheme()
    {
        $theme = $this->sanitizeText($_POST['theme']);
        $_SESSION['theme'] = $theme;
        
        echo json_encode(['success' => false ]);
        die();
    }

    public function subscribe()
    {
        $email = $this->checkEmail($_POST['email']);

        $subscriber = new Subscriber();

        $subscribers = $subscriber->getSubscribers([
            'subscribers.email'
        ],
        ['subscribers.email' => $email ]
        );

        if($subscribers){
            echo json_encode([
                'success' => true,
                'res' => 'email exists'
            ]);
            die(); 
        }else{
            $res = $subscriber->createSubscriber([
                'subscribers.email' => $email
            ]);
            if($res){
                echo json_encode(['success' => true,'res' => $res ]);
                die();
            }
        }

        echo json_encode(['success' => false ]);
        die();
    }

    public function friendRequest()
    {
        $user_id = (int) $_POST['user_id'];
        $requester_id = (int) $_SESSION['User_info']['id'];

        if($user_id){
            $firend = new Friend();
            
            $firends = $firend->getfriend(
                [
                'friends.status', 'friends.recipient', 'friends.requester', 
                ],
                [
                    'friends.recipient' => $user_id,
                    'friends.requester' => $requester_id,
                ]
            );
            if($firends){
                echo json_encode(['success' => false, 
                'msg' => 'Ваш запрос в друзья уже был отправлен' ]);
                die();
            }
            $firends2 = $firend->getfriend(
                [
                'friends.status', 'friends.recipient', 'friends.requester', 
                ],
                [
                    'friends.recipient' => $requester_id,
                    'friends.requester' => $user_id,
                ]
            );

            if ($firends2) {
                echo json_encode(['success' => false, 
                'msg' => 'Вам уже был отправлен запрос в друзья от этого пользователя' ]);
                die();
            }

            $res = $firend->addFriend([
                'friends.recipient' => $user_id,
                'friends.requester' => $requester_id,
            ]);

            echo json_encode(['res' => $res ]);
            die();
        }

        echo json_encode(['success' => false ]);
        die();
    }

    public function sendComplaint()
    {
        $result['success'] = false;
        $args = array(
            'users_id'   => [
              'filter' => FILTER_VALIDATE_INT,
              'flags' => FILTER_NULL_ON_FAILURE, 
            ],
            'body'  => [
              'filter' => FILTER_SANITIZE_STRING,
              'flags' => FILTER_NULL_ON_FAILURE, 
            ],
          );
      
        $params = filter_input_array(INPUT_POST, $args, true);
        
        if(isset($params['users_id']) && $params['users_id'] > 0
            && isset($params['body']) && !empty($params['body'])){
            
                $user = new User();

                $user_info = $user->getUser(
                    ['users.id','users.name','users.email'],
                    ['users.id' => $params['users_id'] ]
                );
                if($user_info){
                    
                    $complaint = new Complaint();
                    $save_complaint = $complaint->createComplaint([
                        'complaints.message' => $params['body'],
                        'complaints.author_id' => $_SESSION['User_info']['id'],
                        'complaints.users_id' => $user_info['id']
                    ]);
                    $author_url = $_ENV['DOMAIN_NAME'].'/author/'.$_SESSION['User_info']['id'];
                    $user_url = $_ENV['DOMAIN_NAME'].'/author/'.$user_info['id'];
                    
                    if($save_complaint){
                        $msg = $this->twig->render('emails/sendComplain.twig', [
                            'msg' => $params['body'],
                            'author_url' => $author_url,
                            'author_name' => $_SESSION['User'],
                            'user' => $user_info,
                            'user_url' => $user_url
                        ]);
                        
                        $this->sendMail([
                            'theme' => 'Жалоба на пользователя',
                            'email' => $_ENV['ADMIN_EMAIL'],
                            'name' => 'admin',
                            'msg' => $msg
                        ]);
            
                        echo json_encode(['success' => true]);
                        die();
                    }
                   
                }
        }

        echo json_encode(['res'=>$result]);
        die();
    }

    public function checkEmailUniqueness()
    {
        $params = filter_input_array(INPUT_POST);
      
        $validation = $this->validation($params);
            
        if( $validation['errors'] ){
            echo json_encode($validation['errors']);
            die();
        }

        $user = new User();
        $result = $user->getUser(
            ['users.email'],
            [ 'users.email' => $validation['data']['email'] ]
        );

        if($result){
            
            $validation['errors']['email_unique'] = 'Данный email уже занят';
        }
    
        if( $validation['errors'] ){
            echo json_encode($validation['errors']);
            die();
        }
  
        echo json_encode(['success' => true]);
        die();
    }

    public function checkSlugUniqueness()
    {
        if(isset($_POST['slug']) && !empty($_POST['slug'])){
            $slug = $this->sanitizeText(strip_tags(trim($_POST['slug'])));
           
            $article = new Article();

            $res = $article->checkSlugUniqueness($slug);
            if($res){
                echo json_encode(['success' => true]);
                die();
            }
            
            echo json_encode(['success' => false]);
            die();
        }
        
    }
    
}
