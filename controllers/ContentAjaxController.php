<?php


namespace controllers;

use models\ContentAjax;
use models\PostModel;
use models\SubsModel;
use models\UsersActions;
use core\Mail;
use core\ValidateAccess;
use core\CRUD;

class ContentAjaxController
{
    public function index($params = null)
    {
        ContentAjax::GetContent('index');
    }

    public function action($params = null)
    {
        ContentAjax::GetContent($params[0]['val']);
    }

    public function changeVote()
    {
        $id = (int) $_GET['id'];
        $vote = (int) $_GET['vote'];
        
        if($id > 0 && $vote !== 0){
            $model = new PostModel();
            $res = $model->changeVote($id, $vote);
            
            if($res) {
                echo json_encode([
                    'success' => true,
                    'likes' => $res['likes'],
                    'dislikes' => $res['dislikes'],
                ]);
                die();
            }

            echo json_encode(['success' => false]);
            die();
        }

        echo json_encode(['success' => false]);
        die();
    }

    public function proposal()
    {

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

        if(ValidateAccess::ValidAccess($this->getCsrf()) && 
            $params['name'] && $params['email'] && $params['tel'] && $params['text']  ){
            $mail_send = new Mail('noreply@sweetspeak.ru', 'maximus2020Free', 'ssl://smtp.yandex.ru', 465, "UTF-8"); // Создаём экземпляр класса
            $from = [
                $params['name'], // Имя отправителя
                $params['email']// почта отправителя
            ];

            $to = 'noreply@sweetspeak.ru';
            $text= "<html>
            <div>
            <p><strong>Имя:</strong>".$params['name']." </p>
            <p><strong>Телефон:</strong> ".$params['tel']." </p>
            <div><strong>Сообщение:</strong> ".$params['text']." </div>
            </div>
            </html>
            ";
            $res = $mail_send->send($to, 'Форма обратной связи', $text, $from);
            if($res){
                echo json_encode(['success' => true]);
                die();
            }
        }

        echo json_encode(['success' => false]);
        die();

    }

    public function sendMessage()
    {
        $args = array(
            'title' => [
                'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags' => FILTER_NULL_ON_FAILURE, 
            ],
            'body'  => [
                'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags' => FILTER_NULL_ON_FAILURE, 
            ],
            'users_id' => [
                'filter' => FILTER_VALIDATE_INT,
                'flags' => FILTER_NULL_ON_FAILURE, 
            ]
        );
      
        $params = filter_input_array(INPUT_GET, $args, true);
        
        if(ValidateAccess::ValidAccess($this->getCsrf()) && isset($_SESSION['User']) &&
            $params['title'] && $params['body'] && $params['users_id'] ){
            
            $messages =  new CRUD('messages');

            $params['author_id'] = (int) $_SESSION['User_info']['id'];
         
            $messages->Add($params);
            
            echo json_encode(['success' => true]);
            die();
            
        }

        echo json_encode(['success' => false]);
        die();
    }

    public function checkEmail($email)
    {
        $filteredEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
        
        if ($filteredEmail) {
            return filter_var($filteredEmail, FILTER_SANITIZE_EMAIL);
        }

        return null;
    }

    public function login()
    {
        $email = $this->checkEmail(trim($_GET['email']));
        $password = filter_var( trim($_GET['password']), FILTER_SANITIZE_SPECIAL_CHARS);

        $filteredEmail = $this->checkEmail($email);

        if($filteredEmail) {
            $params = [];

            array_push($params, 
                [
                    'param' => 'UserEmail',
                    'val' => $filteredEmail,
                ],
                [
                    'param' => 'UserPassword',
                    'val' => $password,
                ],
            );
            
            $res = UsersActions::findUser($params, 'CheckLogin');
            
            echo json_encode([ 'success' => true, 'res' => $res ]);
            die();
        }

        echo json_encode(['success' => false]);
        die();
    }

    public function registration()
    {
        $login = filter_var( trim($_GET['login']), FILTER_SANITIZE_SPECIAL_CHARS);
        $filteredEmail = $this->checkEmail(trim($_GET['email']));
        $password = filter_var( trim($_GET['password']), FILTER_SANITIZE_SPECIAL_CHARS);

        if($filteredEmail) {
            $params = [];

            array_push($params, 
                [
                    'param' => 'UserName',
                    'val' => $login,
                ],
                [
                    'param' => 'UserEmail',
                    'val' => $filteredEmail,
                ],
                [
                    'param' => 'UserPassword',
                    'val' => $password,
                ],
            );
            
            $res = RegisterUser::findUser($params, 'CheckLogin');
            
            echo json_encode([ 'success' => true, 'res' => $res ]);
            die();
        }

        echo json_encode(['success' => false]);
        die();
        
    }

    private function getCsrf()
    {
        $csrf[] = [ 
            'val' => filter_var( $_GET['ValidateFormAccess'], FILTER_SANITIZE_SPECIAL_CHARS),
            'param' => 'ValidateFormAccess'
        ];
        return $csrf;
    }

}