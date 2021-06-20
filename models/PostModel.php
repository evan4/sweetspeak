<?php
/**
 * модель создания индексной страницы
 *
 *
 */

namespace models;

use widgets\Navbar;
use core\Model;
use widgets\TopInfoLine;
use core\CRUD;
use core\Paginator;

class PostModel Extends Model
{
    public $title;
    public $post;
    public $posts;
    public $text;

    public function increaseNumbersVisitors(int $id)
    {
        $post = new CRUD('materials');
        $post_current = $post->GetInfo(array('id'),null,'=',$id,null,0,null)->Resulting[0];
        
        $post->Update(array('see'=> $post_current['see'] + 1),null,'=', array('id'=>$id));
    }

    public function changeVote(int $id, int $vote)
    {
        $post = new CRUD('materials');
        $post_current = $post->GetInfo(array('id'),null,'=',$id,null,0,null)->Resulting[0];
        
        if($vote > 0){
            $post->Update(array('likes' => $post_current['likes'] + 1),null,'=', array('id'=>$id));
        }else{
            $post->Update(array('dislikes' => $post_current['dislikes'] + 1),null,'=', array('id'=>$id));
        }

        return $post->GetInfo(array('id'),null,'=',$id,null,0,null)->Resulting[0];
    }

    public function getAllPosts(array $data)
    {
        $materials = new CRUD('materials');
        $sql = '';
        $sort = '';
        $sort_url = '';

        if(isset($data['sort']['categories']) && !empty($data['sort']['categories'])){
            $categories_ids = '';
        
            $key_parent = array_search($data['sort']['categories'], array_column($categories['all'], 'slug', 'id'));

            foreach ($_SESSION['all_categories']['all'] as $category) {
                if($category['parent'] == $key_parent){
                    
                    $categories_ids .= $category['id'].','; 
                }
                    
            }
            
            $categories_ids = substr($categories_ids, 0, -1);
            
            $sql .= ' AND `categories_id` IN ('.$categories_ids.')';
            
            $sort_url .= '&categories='.$data['sort']['categories'];
        }

        if(isset($data['sort']['datesort']) && !empty($data['sort']['datesort'])){
            $sort_url .= '&datesort='.$data['sort']['datesort'];
            $sql .= ' AND materials.date >= DATE_SUB(CURDATE(), INTERVAL '.$data['sort']['datesort'].' DAY)';
        }
        
        if(isset($data['sort']['popularity'])){
            $sort_url .= '&popularity='. $data['sort']['popularity'];
            $sort = ' ORDER BY materials.likes '. ($data['sort']['popularity'] === 'descending' ? 'DESC' : 'ASC');
        }

        if(isset($data['sort']['comments']) && !empty($data['sort']['comments'])){
            $sort_url .= '&comments='. $data['sort']['comments'];
            $sort = ' ORDER BY comments_count '. ($data['sort']['comments'] === 'descending' ? 'DESC' : 'ASC');;
        }
        
        $str = 'SELECT materials.id, materials.title, materials.slug, materials.date,
            materials.categories_id, materials.snippet,
            materials.photo, materials.see, materials.likes, materials.dislikes, 
            sum(case when comments.status = "1" then 1 else 0 end) as comments_count,
            users.name as author
            FROM `materials` 
            LEFT JOIN `users` ON users.id = materials.author_id 
            LEFT JOIN `comments` ON comments.post_id =materials.id
            WHERE materials.moderated="1" GROUP BY materials.id'.$sort.' LIMIT '.$data['offset'].','.$data['limit'];
        
        $needle = [];
        $indexModel = new IndexModel();
        
        $posts = $indexModel->extendPostData($materials->FreeInfo($str,$needle)->Resulting);
        
        $str = 'SELECT COUNT(id) as total FROM `materials` WHERE materials.moderated="1"'.$sql;
    
        $needle = [];

        $this->posts= [
            'posts' => $posts,
            'total' => $materials->FreeInfo($str,$needle)->Resulting[0]['total']
        ];
        
        $this->title = 'Все стаьи';
        $this->posts['pagination'] = new Paginator(
            $this->posts['total'], $data['limit'], $data['page'], 'Index/posts'.$sort_url
        ); 
      
        $this->template='views/index/posts.php';
    }

    public function getPosts($text, array $data)
    {
        
        $materials = new CRUD('materials');
        $categories =  $_SESSION['all_categories'];
        $sql = '';
        $sort = '';
        $sort_url = '';

        if(isset($data['sort']['categories']) && !empty($data['sort']['categories'])){
            $categories_ids = '';
        
            $key_parent = array_search($data['sort']['categories'], array_column($categories['all'], 'slug', 'id'));

            foreach ($_SESSION['all_categories']['all'] as $category) {
                if($category['parent'] == $key_parent){
                    
                    $categories_ids .= $category['id'].','; 
                }
                    
            }
            
            $categories_ids = substr($categories_ids, 0, -1);
            
            $sql .= ' AND `categories_id` IN ('.$categories_ids.')';
            
            $sort_url .= '&categories='.$data['sort']['categories'];
        }

        if(isset($data['sort']['datesort']) && !empty($data['sort']['datesort'])){
            $sort_url .= '&datesort='.$data['sort']['datesort'];
            $sql .= ' AND materials.date >= DATE_SUB(CURDATE(), INTERVAL '.$data['sort']['datesort'].' DAY)';
        }
        
        if(isset($data['sort']['popularity'])){
            $sort_url .= '&popularity='. $data['sort']['popularity'];
            $sort = ' ORDER BY materials.likes '. ($data['sort']['popularity'] === 'descending' ? 'DESC' : 'ASC');
        }

        if(isset($data['sort']['comments']) && !empty($data['sort']['comments'])){
            $sort_url .= '&comments='. $data['sort']['comments'];
            $sort = ' ORDER BY comments_count '. ($data['sort']['comments'] === 'descending' ? 'DESC' : 'ASC');;
        }
        
        $str = 'SELECT materials.id, materials.title, materials.slug, materials.date, materials.author_id,
            materials.categories_id, materials.snippet,
            materials.photo, materials.see, materials.likes, materials.dislikes, 
            sum(case when comments.status = "1" then 1 else 0 end) as comments_count,
            users.name as author
            FROM `materials` 
            LEFT JOIN `users` ON users.id = materials.author_id 
            LEFT JOIN `comments` ON comments.post_id =materials.id
            WHERE materials.title LIKE \'%'.$text.'%\' '.$sql.'  
            AND materials.moderated="1" GROUP BY materials.id'.$sort.' LIMIT '.$data['offset'].','.$data['limit'];
        
        $needle = array();
        $indexModel = new IndexModel();
        
        $posts = $indexModel->extendPostData($materials->FreeInfo($str,$needle)->Resulting);
        
        $str = 'SELECT COUNT(id) as total FROM `materials` 
        WHERE materials.title LIKE \'%'.$text.'%\' AND materials.moderated="1"';
    
        $needle = array();

        $this->posts= [
            'posts' => $posts,
            'total' => $materials->FreeInfo($str,$needle)->Resulting[0]['total']
        ];
        
        $this->title = 'Поиск '.$text;
        $this->text = $text;
        $this->posts['pagination'] = new Paginator(
            $this->posts['total'], $data['limit'], $data['page'], '/search?search='.$text.$sort_url
        ); 
      
        $this->template='views/showroom/search.php';
    }

    public function getPost($article_slug)
    {
        $materials = new CRUD('materials');
        $str = 'SELECT materials.id, materials.title, materials.titleseo, materials.description,
        materials.slug, materials.date, materials.author_id,
        materials.categories_id, materials.content, materials.snippet,
        materials.photo, materials.see, materials.likes, materials.dislikes, 
        sum(case when comments.status = "1" then 1 else 0 end) as comments_count,
        users.name as author
        FROM `materials` 
        LEFT JOIN `users` ON users.id = materials.author_id 
        LEFT JOIN `comments` ON comments.post_id = materials.id
        WHERE materials.slug = "'.$article_slug.'" AND materials.moderated="1" GROUP BY materials.id';
        
        $needle = array();
        $indexModel = new IndexModel();
        $post = $materials->FreeInfo($str,$needle)->Resulting;
        
        $this->post = $indexModel->extendPostData($post);
        
        $this->template='views/showroom/defaultpost.php';
    }

    public function getRealatedPosts(int $category, int $id)
    {
        $materials = new CRUD('materials');
        $str = 'SELECT materials.id, materials.title, materials.slug, materials.date, materials.author_id,
        materials.categories_id, materials.snippet,
        materials.photo, materials.see, materials.likes, materials.dislikes, 
        sum(case when comments.status = "1" then 1 else 0 end) as comments_count,
        users.name as author
        FROM `materials` 
        LEFT JOIN `users` ON users.id = materials.author_id 
        LEFT JOIN `comments` ON comments.post_id = materials.id
        WHERE materials.categories_id = '.$category.' AND  materials.id <> '.$id.' AND materials.moderated="1" GROUP BY materials.id LIMIT 4';
        
        $needle = array();
        $indexModel = new IndexModel();
        return $indexModel->extendPostData($materials->FreeInfo($str,$needle)->Resulting);
    }

    public function getComments(int $id)
    {
       
        $comments = new CRUD('comments');
        $str = 'SELECT comments.id, comments.body, comments.parent, comments.created_at,
        comments.author_id, users.name as author, users.photo
        FROM `comments` 
        LEFT JOIN `users` ON comments.author_id = users.id 
        WHERE comments.status="1" AND comments.post_id=\''.$id.'\' GROUP BY comments.id  
        ORDER BY comments.parent ASC';

        $needle = array();
        
        $comments_arrays = $this->extendComments($comments->FreeInfo($str,$needle)->Resulting);
        
        $comment_photos = new CRUD(' comment_photos');
        $comments_array = [];
        
        for ($i=0; $i < count($comments_arrays); $i++) { 
           
            $str = 'SELECT comment_photos.photo
            FROM `comment_photos` 
            WHERE `comment_id`='.$comments_arrays[$i]['id'];
            $needle = array();
            $comments_arrays[$i]['photos'] = $comment_photos->FreeInfo($str,$needle)->Resulting;
            
            if($comments_arrays[$i]['parent'] == 0){
                
                $comments_array[$comments_arrays[$i]['id']][] = $comments_arrays[$i];
             
            }else{
                $comments_array[$comments_arrays[$i]['parent']][] = $comments_arrays[$i];
            }
            
        }
        return $comments_array;

    }

    public function popular(int $limit)
    {
        $posts = new CRUD('materials');
        $popular = $posts->GetInfo(array('moderated'),null,'=',1,3,0,'ORDER BY comments_count DESC', $limit)->Resulting;
        return $popular;
    }

    public function extendComments(array $comments)
    {
        $now = new \DateTime("today");

        $sorted_comments = [];

        for($i=0; $i < count($comments) ; $i++) {
           
            $comments[$i]['created_at'] = $this->dateFormat($comments[$i]['created_at'], $now);

        }
       
        return $comments;
    }

    public function getPostsNumbers()
    {
      $posts_model = new CRUD('materials');
      $str = 'SELECT COUNT(id) as total FROM `materials` WHERE `moderated`="1"';
    
      $needle = array();
      return $posts_model->FreeInfo($str,$needle)->Resulting[0]['total'];
    }

    public function getPostsByLimit(array $data)
    {
        $posts_model = new CRUD('materials');

        $str = 'SELECT materials.id, materials.slug, materials.updated_at
        FROM `materials` 
        WHERE `moderated`="1" ORDER BY materials.id LIMIT '.$data['offset'].','.$data['limit'];
    
        $needle = array();
        
        $posts = $posts_model->FreeInfo($str,$needle)->Resulting;
  
        $needle = array();
        
        return $posts;
    }

    public function getLastArticleCreateDateByCategoryId(int $id)
    {
      $materials = new CRUD('materials');
      $str = 'SELECT date
        FROM `materials` 
        WHERE categories_id=\''.$id.'\' AND id=(SELECT max(ID) FROM materials)
        AND `moderated`="1"';
  
      $needle = array();

      return $materials->FreeInfo($str,$needle)->Resulting;

    }

    public function lastArticleDate()
    {
        $materials = new CRUD('materials');
        $str = 'SELECT date
        FROM `materials` 
        WHERE `moderated`="1" ORDER BY id DESC LIMIT 1';
  
        $needle = array();

        return $materials->FreeInfo($str,$needle)->Resulting[0];
    }

    public function getLastArticleCreateDateByCategoriesId( $categories_ids)
    {
      $materials = new CRUD('materials');
      $str = 'SELECT date
        FROM `materials` 
        WHERE `categories_id` IN ('.$categories_ids.') AND `moderated`="1"
        ORDER BY id DESC LIMIT 1';
        
        $needle = array();
      return $materials->FreeInfo($str,$needle)->Resulting;

    }

    public function getLastArticleCreateDateByAuthorId(int $id)
    {
      $materials = new CRUD('materials');
      $str = 'SELECT id, date
        FROM `materials` 
        WHERE author_id=\''.$id.'\' AND `moderated`="1" ORDER BY id DESC LIMIT 1';
  
      $needle = array();
    
      return $materials->FreeInfo($str,$needle)->Resulting;

    }
}
