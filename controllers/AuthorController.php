<?php


namespace controllers;

use models\AuthorModel;

class AuthorController extends Controller
{

    public function index()
    {
        $limit = 9;
        $page = (empty($_GET['page']) ? 1 : intval($_GET['page']));
        $offset = $page === 1 ? 0 : ($page - 1) * $limit;
        
        $url = 'authors';

        $model = new AuthorModel();

        $data = $model->index([
            'limit' => $limit,
            'url' => $url,
            'offset' => $offset,
            'page' => $page
        ]);
       
        echo $this->twig->render('author/index.twig',[
            'title' => 'Все авторы',
            'authors' => $data
        ]);

    }

    public function author($id)
    {
   
        if(is_numeric($id) && $id > 0){
            $model = new AuthorModel();
            $url = 'authors/'.$id;
            $data = $model->getAuthor($id);

            $friend = false;
            
            foreach ($data['friends'] as $friend) {
              $friend = (
                  isset($_SESSION['User_info']) && 
                $friend['requester'] === intval($_SESSION['User_info']['id']) &&
                    $friend['recipient'] === intval($data['id']) 
                ) || 
                (
                  $friend['requester'] === intval($data['id'])  &&
                  $friend['recipient'] ===  intval($_SESSION['User_info']['id'])
                )  ? true : false;
              if ($friend) break;
            }
            
            echo $this->twig->render('author/author.twig',[
                'user_login' => isset($_SESSION['User']) ? true : false,
                'title' =>  'Автор '.$data['name'],
                'author' => $data,
                'friend' => $friend
            ]);
        }else{
            require '404.php';
        }
        
    }
    
    public function articles($id)
    {
        $page = (empty($_GET['page']) ? 1 : intval($_GET['page']));
        $offset = $page === 1 ? 0 : ($page - 1) * 9;

        $sort = [];

        if(isset($_GET['categories']) && !empty($_GET['categories'])){
            $sort['categories']= filter_var( trim($_GET['categories']), FILTER_SANITIZE_SPECIAL_CHARS);
        }

        if(isset($_GET['datesort']) && !empty($_GET['datesort'])){
            $sort['datesort']= filter_var( trim($_GET['datesort']), FILTER_SANITIZE_SPECIAL_CHARS);
        }

        if(isset($_GET['popularity']) && !empty($_GET['popularity'])){
            $sort['popularity']= filter_var( trim($_GET['popularity']), FILTER_SANITIZE_SPECIAL_CHARS);
        }

        if(isset($_GET['comments']) && !empty($_GET['comments'])){
            $sort['comments']= filter_var( trim($_GET['comments']), FILTER_SANITIZE_SPECIAL_CHARS);
        }

        $model = new AuthorModel(); // Создание экземпляра класса
        
        if($id > 0){
            $url = 'author/articles/'.$id;
            
            $data = $model->getAuthorArticles($id,[
                'url' => $url,
                'sort' => $sort,
                'limit' => 12,
                'offset' => $offset,
                'page' => $page
            ]);
            
            echo $this->twig->render('author/articles.twig',[
                'title' =>  'Статьи '.$data['name'],
                'author' => $data
            ]);
            
        }else{
            require '404.php';
        }
    }
}