<?php

namespace controllers;

use models\IndexModel;
use models\LkModel;
use models\UsersModel;

final class IndexController extends Controller
{
    public function index()
    {
        $model = new IndexModel();
        $title = 'SweetSpeak — социальная сеть для анонимного общения на интимные темы';
        $description = "Подборка статей о знакомствах, любви, сексе и отношениях, с советами о том, как сохранить здоровые и счастливые взаимоотношения";
        $user_login = (isset($_SESSION['User']) && !empty($_SESSION['User']) ) ? true : false;

        if(isset($_SESSION['User_info']) && !empty($_SESSION['User_info'])){
            $data = $model->index();
            
            echo $this->twig->render('home/index.twig', [
                'popularPosts' => $data['popularPosts'],
                'categories_info' => $data['categories_info'],
                'user_login' => $user_login,
                'title' => $title,
                'description' => $description
            ]);
        }else{
            $popularPosts = $model->getPopularPosts(10);
            
            $totalPosts = $model->totalNumberOfPosts();
            echo $this->twig->render('home/guest.twig', compact(
                'title','description', 'popularPosts', 'totalPosts'));
        }
        
    }

    public function blog()
    {

        echo $this->twig->render('home/blog.twig', [
            'title' => 'Блог',
            
        ]);
    }

    public function about()
    {
        echo $this->twig->render('home/about.twig');
    }

    public function rules() 
    {
        echo $this->twig->render('home/rules.twig');
    }

    public function privacy() 
    {
        echo $this->twig->render('home/privacy.twig');
    }

    public function posts()
    {
        $model = new IndexModel();
        $title = 'SweetSpeak — социальная сеть для анонимного общения на интимные темы';
        $description = "Подборка статей о знакомствах, любви, сексе и отношениях, с советами о том, как сохранить здоровые и счастливые взаимоотношения";
        $data = $model->index();

        echo $this->twig->render('home/posts.twig', [
            'popularPosts' => $data['popularPosts'],
            'categories_info' => $data['categories_info'],
            'title' => $title,
            'description' => $description
        ]);

    }

    public function article()
    {
        if(!isset($_SESSION['User_info']) || empty($_SESSION['User_info']) ||
        (int) $_SESSION['User_info']['confirm'] != 1
        ){
            include('403.php');
        }else {
            echo $this->twig->render('articleCreate.twig');
        }
    }

    public function MyPosts($params=null)
    {
        $model= new LkModel(); // Модель принимает на себя данные для ЛК
        $model->MyPosts(); // Генерируется страница постов пользователя

        include('views/lk/myposts.php'); // подключается вид
    }

    public function getPosts()
    {
        $data = [
            'category' => (int) $_GET['category'],
            'limit' => (int) $_GET['limit'],
            'id'=>  isset($_GET['parent']) ? (int) $_GET['category'] : null
        ];
        
        $model = new IndexModel();
        $posts = $model->getPosts($data);
        
        echo json_encode($posts);
        die();
    }

    public function recoveryPass()
    {
        $result = 'Ссылка недействительна';
        $form = false;
        $user = new UsersModel();

        if(isset($_GET['id']) && !empty($_GET['id']) &&
        isset($_GET['signature']) && !empty($_GET['signature'])){
           $data = [
            'id' => (int) $_GET['id'],
            'signature' => filter_var( $_GET['signature'], FILTER_SANITIZE_SPECIAL_CHARS)
            ];
            $id = $data['id'];
            $res = $user->getuserById($data['id']);
            
            if($res){
                $now = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));
                
                $created_at = strtotime('+1 day', $res['verify_token_timestamp']);
                
                if($created_at < $now){
                    $result = 'Ссылка устарела. Пройдите регистрацию заново';
                    
                } elseif(password_verify($data['signature'],$res['remember_token'])){
                    $form = true;
                    $result = '';
                }
                
            }
            
        }

        echo $this->twig->render('home/recoveryPassword.twig', compact('form', 'result', 'id'));
    }

    public function recoveryPassword()
    {
        $result = 'Ссылка недействительна';
        $form = false;
        $user = new UsersModel();
        
        if(isset($_POST['password']) && !empty($_POST['password']) && 
        isset($_POST['id']) && !empty($_POST['id'])){

            $res = $user->getuserById((int)$_POST['id']);

            if($res && $res['remember_token']){

                $res = $user->updatePassword([
                    'id' => (int) $_POST['id'],
                    'password' => filter_var( $_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS)
                ]);
                $result = 'Пароль изменен.';
            }else{
                $form = true;
                $result = 'Произошла ошибка при сохранении данных. Попробуйте снова.';
            }

        }
        echo $this->twig->render('home/recoveryPassword.twig', compact('form', 'result', 'id'));
    }

    public function verifyEmail()
    {
        $result = 'Ссылка недействительна';

        if(isset($_GET['id']) && !empty($_GET['id']) &&
        isset($_GET['signature']) && !empty($_GET['signature'])){
           $data = [
            'id' => (int) $_GET['id'],
            'signature' => filter_var( $_GET['signature'], FILTER_SANITIZE_SPECIAL_CHARS)
            ];

            $user = new UsersModel();
            $res = $user->getuserById($data['id']);
            
            if($res){
                $now = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));
                
                $created_at = strtotime('+1 day', $res['created_at']);
                
                if($created_at < $now){
                    $result = 'Ссылка устарела. Пройдите регистрацию заново';
                    $user->delete($data['id']);
                } elseif(password_verify($data['signature'],$res['remember_token'])){
                    $result = 'Вы успешнно активировали свой аккаунт. Войдите на сайт, используя свой логин и пароль';
                    $userModel = new UsersModel();
                    $userModel->activate($data['id']);
                }
                
            }
            
        }

        echo $this->twig->render('home/verifyEmail.twig', compact('result'));
        
    }

}
