<?php
/**
 * модель создания индексной страницы
 *
 *
 */

namespace models;

use core\CRUD;
use core\Model;
use widgets\TopInfoLine;
use controllers\GetContentController;
use models\UsersModel;
use models\CategoryTable;

class IndexModel Extends Model
{

    public $content;
    public $title;
    public $script;
    public $alert;
    public $FirstSection;
    public $SocialContent;
    
    public  function index($logined=false) 
    {

        $this->title='SweetSpeak - сайт статей о знакомствах';

        $hots = new CRUD('materials');

         $str = 'SELECT materials.id, materials.title, materials.slug, materials.date,
            materials.snippet, materials.categories_id,
            materials.photo, materials.see, materials.likes, materials.dislikes,
            materials.author_id,
            sum(case when comments.status = "1" then 1 else 0 end) as comments_count,
            users.name as author
            FROM `materials` 
            LEFT JOIN `users` ON users.id = materials.author_id 
            LEFT JOIN `comments` ON comments.post_id = materials.id
            WHERE `moderated`="1" GROUP BY materials.id ORDER BY materials.date DESC LIMIT 5';

        $needle = array();
    
        $popularPosts = $this->extendPostData($hots->FreeInfo($str,$needle)->Resulting);
        
        $categories = $_SESSION['all_categories'];
        
        $categories_info = $categories['categories'];
        
        $subcategories = $categories['subcategories'];

        foreach ($categories_info as $key => $category) {
            
            switch ($key) {
                case 'Знакомства и свидания':
                case 'Одежда и аксесуары':
                case 'Интересное и полезное':
                    $limit = 3;
                    break;
                case 'Отношения в парах':
                case 'Обучение':
                    $limit = 2;
                    break;
                case 'Тусовки':
                case 'BDSM и Фетиш':
                    $limit = 4;
                    break;
                default:
                    $limit = 4;
                    break;
            }

            $sub = array_key_first($subcategories[$category]);
            
            $key_sub = array_search($sub, array_column($categories['all'], 'name', 'id'));
            
            $key_in_all = array_search($key_sub, array_column($categories['all'], 'id'));
            $key_parent = $categories['all'][$key_in_all]['parent'];

            $data = [
                'id' => $key_parent,
                'limit' => $limit
            ];
            
            $posts = $this->getLatestPostsByParentCategory($data)['posts'];

            $subs = [];
            
            foreach ($subcategories[$category] as $key_sub => $value) {
                array_push($subs , [
                    'id' => array_search($key_sub, array_column($categories['all'], 'name', 'id')),
                    'slug' => $value,
                    'name' => $key_sub
                ]);
            }
            
            $categories_info[$key] = [
                'title' => $key,
                'id' => array_search($category, array_column($categories['all'], 'slug', 'id')),
                'url' => array_search($category, array_column($categories['all'], 'slug', 'slug')),
                'subcategory' => $subs,
                'posts' => $posts,
                'total' => $this->totaPostslByMainCategory($key_parent)
            ];
            

        }   
 
        return [
            'popularPosts' => $popularPosts,
            'categories_info' => $categories_info
        ];
    }

    public function totalNumberOfPosts()
    {
        $hots = new CRUD('materials');

        $str = 'SELECT COUNT(id) as total FROM `materials`  WHERE  materials.moderated="1"';
        $needle = [];
        
        return $hots->FreeInfo($str,$needle)->Resulting[0]['total'];

    }

    
    public function getPopularPosts(int $limit)
    {
       $hots = new CRUD('materials');

        $str = 'SELECT materials.id, materials.title, materials.slug, materials.date,
           materials.categories_id, materials.snippet,
           materials.photo, materials.see, materials.likes, materials.dislikes,
           materials.author_id, users.photo as author_photo,
           sum(case when comments.status = "1" then 1 else 0 end) as comments_count,
           users.name as author
           FROM `materials` 
           LEFT JOIN `users` ON users.id = materials.author_id 
           LEFT JOIN `comments` ON comments.post_id = materials.id
           WHERE `moderated`="1" GROUP BY materials.id ORDER BY materials.date DESC LIMIT '.$limit;

        $needle = [];
        
        return $this->popularPosts = $this->extendPostData($hots->FreeInfo($str,$needle)->Resulting);

    }

    public function popularPosts(int $limit)
    {
        $hots = new CRUD('materials');

        $str = 'SELECT materials.id, materials.title, materials.slug, materials.date,
           materials.categories_id, materials.snippet,
           materials.photo, materials.see, materials.likes, materials.dislikes,
           materials.author_id, users.photo as author_photo,
           sum(case when comments.status = "1" then 1 else 0 end) as comments_count,
           users.name as author
           FROM `materials` 
           LEFT JOIN `users` ON users.id = materials.author_id 
           LEFT JOIN `comments` ON comments.post_id = materials.id
           WHERE `moderated`="1" GROUP BY materials.id ORDER BY comments_count ASC LIMIT '.$limit;

        $needle = [];
        
        return $this->popularPosts = $this->extendPostData($hots->FreeInfo($str,$needle)->Resulting);

    }

    public function totaPostslByMainCategory($id)
    {
        $materials = new CRUD('materials');
        $category_id = (int) $id;

        $sql = '';
        
        foreach ($_SESSION['all_categories']['all'] as $category) {
            if($category['parent'] == $id){
                
                $sql .= $category['id'].','; 
            }
                
        }
        
        $sql = substr($sql, 0, -1);
        
        $str = 'SELECT COUNT(id) as total FROM `materials` 
        WHERE  `moderated` ="1" AND `categories_id` IN('.$sql.')';
        
        $needle = array();
        $res = $materials->FreeInfo($str,$needle)->Resulting[0];
       
        return $res['total'];
    }

    public function getPopularCategories(int $limit)
    {

        $materials = new CRUD('materials');

        $categories = $_SESSION['all_categories'];

        $total_by_categories= [];
        foreach($categories['categories'] as $key => $categoriy){
            $id = array_search($key, array_column($categories['all'], 'name'));
            $total_posts = $this->totaPostslByMainCategory($id);
            if($total_posts){
                $total_by_categories[$key]= $total_posts;
            }
            
        }
        
        $categories_popular = [];
        
        for ($i=0; $i < count($total_by_categories); $i++) { 
            if(count($categories_popular) > $limit) break;
            $key = array_search(max($total_by_categories),$total_by_categories);
            
            if($total_by_categories[$key]) {
                $categories_popular[] = [
                    'title' => $key,
                    'url' => '/'.$categories['categories'][$key],
                    'total' => $total_by_categories[$key]
                ];
                unset($total_by_categories[$key]);
            }
        }
       
        return $categories_popular;

    }

    public function getPosts(array $data)
    {
        if(isset($data['id'])){
            $this->getLatestPostsByParentCategory($data);
        }else{
            $materials = new CRUD('materials');
            $str = 'SELECT materials.id, materials.title, materials.slug, materials.date,
                materials.categories_id, materials.snippet,
                materials.photo, materials.see, materials.likes, materials.dislikes,
                materials.author_id,
                sum(case when comments.status = "1" then 1 else 0 end) as comments_count,
                users.name as author
                FROM `materials` 
                LEFT JOIN `users` ON users.id = materials.author_id 
                LEFT JOIN `comments` ON comments.post_id =materials.id
                WHERE `categories_id` ="'.$data['category'].'"
                AND `moderated`="1" GROUP BY materials.id ORDER BY materials.date DESC LIMIT '.$data['limit'];
            
            $needle = array();
            
            $posts = $this->extendPostData($materials->FreeInfo($str,$needle)->Resulting);
    
            if($this->checkAjax()){
                echo json_encode($posts);
                die();
            }
    
            $posts= [
                'posts' => $posts,
                'total' => $materials->FreeInfo($str,$needle)->CurrentRows
            ];
    
            return $posts;
        }
      
    }
    
    public function getLatestPostsByParentCategory(array $data)
    {
        $sql = '';
        
        foreach ($_SESSION['all_categories']['all'] as $category) {
            if($category['parent'] == $data['id']){
                
                $sql .= $category['id'].','; 
            }
                
        }
            
        $sql = substr($sql, 0, -1);

        $materials = new CRUD('materials');
        $str = 'SELECT materials.id, materials.title, materials.slug, materials.date,
            materials.categories_id, materials.snippet,
            materials.photo, materials.see, materials.likes, materials.dislikes,
            materials.author_id,
            sum(case when comments.status = "1" then 1 else 0 end) as comments_count,
            users.name as author
            FROM `materials` 
            LEFT JOIN `users` ON users.id = materials.author_id 
            LEFT JOIN `comments` ON comments.post_id =materials.id
            WHERE `moderated` ="1" AND `categories_id` IN('.$sql.') GROUP BY materials.id ORDER BY materials.date DESC LIMIT '.$data['limit'];
        
        $needle = array();
        
        $posts = $this->extendPostData($materials->FreeInfo($str,$needle)->Resulting);

        if($this->checkAjax()){
            echo json_encode($posts);
            die();
        }
        
        $posts= [
            'posts' => $posts,
            'total' => $materials->FreeInfo($str,$needle)->CurrentRows
        ];

        return $posts;
    }

}
