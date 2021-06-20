<?php


namespace models;

use core\CRUD;
use core\Model;
use core\Paginator;
use models\IndexModel;

class CategoryModel extends Model
{
    public $post;
    public $posts;
    public $category;
    public $comments;
    public $page;

    public function geteData(array $category)
    {
        $materials = new CRUD('materials');
        
        $indexModel = new IndexModel();
        $limit = 12;
        $categories = $_SESSION['all_categories'];
        $icon = [];
        $icons = require './config/category_icons.php';

        $i = -1;
        foreach ($categories['categories'] as $key => $cat) {
            $i++;
            $icon[$key] = $icons[$i];
        }

        $this->page = (empty($_GET['page']) ? 1 : intval($_GET['page']));
        $offset = $this->page === 1 ? 0 : ($this->page - 1) * $limit;

        $sort = '';

        if(isset($category['sort']['datesort']) && !empty($category['sort']['datesort'])){
            $sort .= '&datesort='.$this->category['sort']['datesort'];
        }
        
        if(isset($category['sort']['popularity'])){
            $sort .= '&popularity='. $category['sort']['popularity'];
        }

        if(isset($category['sort']['comments']) && !empty($category['sort']['comments'])){
            $sort .= '&comments='. $category['sort']['comments'];
        }
        
        if(isset($sort)){
            $sort_new = substr_replace($sort, '?', 0, 1);
        }else{
            $sort_new = '';
        }

        if($category['parent']){
            
            $data =[
                'category' => $category['parent']['title'],
                'sort' => isset($category['sort']) ? $category['sort'] : [],
                'subcategory' => $category['subcategory_current']['title'],
                'subcategory_id' => $category['subcategory_current']['id'],
                'limit' => $limit,
                'offset' => $offset,
            ];
            
            $this->posts = $this->getСategoryPosts($data);

            $this->category['pagination'] = new Paginator(
                $this->posts['total'], $limit, $this->page, '/articles/'.
                $category['parent']['code'].'/'.$category['subcategory_current']['code'].$sort_new
            );
           
            $this->category['icon'] = $icon[$data['category']];
            $this->category['parent']['title'] = $category['parent']['title'];
            $this->title = $data['category'] .' - '.$data['subcategory'];
            $this->template='views/showroom/defaultsubcategory.php';
        }else{
            
            $data =[
                'category' => $category['category_current']['title'],
                'category_id' => $category['category_current']['id'],
                'sort' => $category['sort'],
                'limit' => $limit,
                'offset' => $offset
            ];
            
            $this->posts = $this->getСategoryPosts($data);
           
            $this->category['pagination'] = new Paginator(
                $this->posts['total'], $limit, $this->page, '/articles/'.$category['category_current']['code'].$sort_new
            );
            
            $this->category['icon'] = $icon[$data['category']];
            $this->category['category_current']['title'] = $category['category_current']['title'];
            $this->title = $data['category'];
            
            $this->template='views/showroom/defaultcategory.php';
        }

    }

     public function getСategoryPosts(array $data)
    {
        
        $categories = $_SESSION['all_categories'];
        
        $materials = new CRUD('materials');
       
        $sql = '';

        if(isset($data['subcategory_id']) && !empty($data['subcategory_id'])){
            $sql .= ' AND `categories_id`=\''.$data['subcategory_id'].'\'';
            
        }else{
        
            foreach ($categories['all'] as $category) {
                if($category['parent'] == $data['category_id']){
                    
                    $sql .= $category['id'].','; 
                }
                    
            }
            $sql = substr($sql, 0, -1);
            $sql = ' AND `categories_id` IN ('.$sql.')';
        }
        
        if(count($data['sort'])){
            
            $sort = '';

            if(isset($data['sort']['datesort']) && !empty($data['sort']['datesort'])){
                $sql .= ' AND materials.date >= DATE_SUB(CURDATE(), INTERVAL '.$data['sort']['datesort'].' DAY)';
            }
            
            if(isset($data['sort']['popularity'])){
                $sort = ' ORDER BY materials.likes '. ($data['sort']['popularity'] === 'descending' ? 'DESC' : 'ASC');
            }

            if(isset($data['sort']['comments']) && !empty($data['sort']['comments'])){
                $sort = ' ORDER BY comments_count '. ($data['sort']['comments'] === 'descending' ? 'DESC' : 'ASC');;
            }
        }

        if(!isset($sort)){
            $sort = ' ORDER BY date DESC';
        }

        $str = 'SELECT materials.id, materials.title, materials.slug, materials.date,
            categories_id, materials.snippet,
            materials.photo, materials.see, materials.likes, materials.dislikes, 
            materials.author_id,
            sum(case when comments.status = "1" then 1 else 0 end) as comments_count,
            users.name as author
            FROM `materials` 
            LEFT JOIN `users` ON users.id = materials.author_id 
            LEFT JOIN `comments` ON comments.post_id = materials.id
            WHERE `moderated`="1" '.$sql.' 
             GROUP BY materials.id'.$sort.' LIMIT '.$data['offset'].','.$data['limit'];
        
        $needle = array();
        
        $posts = $this->extendPostData($materials->FreeInfo($str,$needle)->Resulting);

        if($this->checkAjax()){
            echo json_encode($posts);
            die();
        }

        $str = 'SELECT COUNT(id)  as total FROM `materials` 
        WHERE `moderated`="1" '.$sql.'';
    
        $needle = array();

        $posts= [
            'posts' => $posts,
            'total' => $materials->FreeInfo($str,$needle)->Resulting[0]['total']
        ];

        return $posts;
    }

    public function getPost(int $id, array $category)
    {
        $materials = new CRUD('materials');
        
        $str = 'SELECT materials.id, materials.title, materials.slug, materials.date,
            materials.categories_id, materials.snippet, 
            materials.content, 
            materials.photo, materials.see, materials.likes, materials.dislikes, 
            materials.dislikes, materials.author_id,
            users.name as author
            FROM `materials` 
            LEFT JOIN `users` ON materials.author_id = users.id 
            WHERE materials.id=\''.$id.'\' AND `moderated`="1" GROUP BY materials.id ';

        $needle = array();
        $indexModel = new IndexModel();
        
        $this->post = $indexModel->extendPostData($materials->FreeInfo($str,$needle)->Resulting)[0];
        
        if(!$this->post) {
            $this->template='404.php';
        }else{
            $this->title = $this->post['title'];
            $this->category = $category;

            $comments = new CRUD('comments');
            $str = 'SELECT comments.id, comments.body, comments.parent, comments.created_at,
            comments.author_id, users.name as author, users.photo
            FROM `comments` 
            LEFT JOIN `users` ON comments.author_id = users.id 
            WHERE comments.status="1" AND comments.post_id=\''.$this->post['id'].'\' GROUP BY comments.id  
            ORDER BY comments.parent ASC';

            $needle = array();
            
            $this->comments = $this->extendComments($comments->FreeInfo($str,$needle)->Resulting);
            
            $comment_photos = new CRUD(' comment_photos');
            $comments_array = [];
            
            for ($i=0; $i < count($this->comments); $i++) { 
               
                $str = 'SELECT comment_photos.photo
                FROM `comment_photos` 
                WHERE `comment_id`='.$this->comments[$i]['id'];
                $needle = array();
                $this->comments[$i]['photos'] = $comment_photos->FreeInfo($str,$needle)->Resulting;
                
                if($this->comments[$i]['parent'] == 0){
                    
                    $comments_array[$this->comments[$i]['id']][] = $this->comments[$i];
                 
                }else{
                    $comments_array[$this->comments[$i]['parent']][] = $this->comments[$i];
                }
                
            }
            $this->comments = $comments_array;

            $sql = ' AND `categories_id`=\''.$category['subcategory_current']['id'].'\'';
            
            // Похожие статьи
            $str = 'SELECT materials.id, materials.title, materials.slug, materials.date,
                materials.categories_id, materials.snippet,
                materials.photo, materials.see, materials.likes, materials.dislikes,
                materials.author_id,
                sum(case when comments.status = "1" then 1 else 0 end) as comments_count,
                users.name as author
                FROM `materials` 
                LEFT JOIN `users` ON users.id = materials.author_id 
                LEFT JOIN `comments` ON comments.post_id =materials.id
                WHERE  materials.id <>\''.$id.'\''.$sql.' AND `moderated`="1" GROUP BY materials.id LIMIT 4';
            
            $needle = array();
            $indexModel = new IndexModel();
            $this->posts = $indexModel->extendPostData($materials->FreeInfo($str,$needle)->Resulting);
            
            $this->template='views/showroom/defaultpost.php';
        }

    }

    public function extendComments(array $comments)
    {
        $now = new \DateTime("today");

        $sorted_comments = [];

        for($i=0; $i < count($comments) ; $i++) {
           
            $comments[$i]['created_at'] = $this->dateFormat($posts[$i]['created_at'], $now);

        }
       
        return $comments;
    }

}
