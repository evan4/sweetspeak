<?php
/**
 * модель создания индексной страницы
 *
 *
 */

namespace models;

use core\CRUD;
use core\Model;
use core\Paginator;
use models\UsersActions;

class AuthorModel Extends Model
{

    public  function index(array $data)
    {
      $author_model = new CRUD('users');

      $str = 'SELECT users.id, users.name, users.photo, users.motto, users.userstatus,
      sum(case when materials.moderated = "1" then 1 else 0 end) as posts,
      sum(case when comments.status = "1" then 1 else 0 end) as comments
      FROM `users` 
      LEFT JOIN `materials` ON materials.author_id = users.id
      LEFT JOIN `comments` ON comments.author_id = users.id
      WHERE `confirm`=1 GROUP BY users.id ORDER BY users.id DESC LIMIT '.$data['offset'].','.$data['limit'];
  
      $needle = array();
      
      $authors = $author_model->FreeInfo($str,$needle)->Resulting;

      $str = 'SELECT COUNT(id) as total FROM `users` WHERE `confirm`=1';
    
      $needle = array();
      
      $authors = [
        'authors' => $authors,
        'total' => $author_model->FreeInfo($str,$needle)->Resulting[0]['total']
      ];
     
      $authors['pagination'] = new Paginator(
        $authors['total'], $data['limit'], $data['page'], $data['url']
      );

      return $authors;
    }

    public function getAuthors(array $data)
    {
      $author_model = new CRUD('users');

      $str = 'SELECT users.id, users.updated_at
      FROM `users` 
      WHERE `confirm`="1" ORDER BY users.id LIMIT '.$data['offset'].','.$data['limit'];
  
      $needle = array();
      
      $authors = $author_model->FreeInfo($str,$needle)->Resulting;

      $needle = array();
      
      return $authors;
    }

    public function getAuthorsNumbers()
    {
      $author_model = new CRUD('users');
      $str = 'SELECT COUNT(id) as total FROM `users` WHERE `confirm`="1"';
    
      $needle = array();
      return $author_model->FreeInfo($str,$needle)->Resulting[0]['total'];
    }

    public function getAuthor(int $id)
    {
      $authors_model = new CRUD('users');
      $str = 'SELECT users.id, users.name, users.photo, users.status, users.userstatus,
      users.age, users.city, users.bio,users.website,
      users.created_at, users.instagram, users.vk, users.facebook, users.motto,
      sum(case when materials.moderated = "1" then 1 else 0 end) as articles
      FROM `users` 
      LEFT JOIN `materials` ON materials.author_id = users.id 
      WHERE users.id ='.$id.' GROUP BY users.id';
  
      $needle = array();
      
      $authors = $authors_model->FreeInfo($str,$needle)->Resulting[0];
      
      if(!$authors){
          require '404.php';
      }
      
      $authors['gender'] = (isset($authors['gender']) && $authors['gender'] === 'f') ? 'Женский' : 'мужской';
      $photos = new CRUD('user_photos');

      $users_photos = $photos->GetInfo(array('author_id'), null, '=', $id, null, 0, null)->Resulting;
      $authors['users_photos'] = [];

      for ($i=0; $i < count($users_photos); $i++) { 
        $authors['users_photos'][$i] = [
          'large' =>'/bundles/users/1000_'.$users_photos[$i]['photo'],
          'medium' =>'/bundles/users/300_'.$users_photos[$i]['photo'],
        ];
      }
     
      $last_num = ($authors['age'] % 10);

      if($authors['age']){
        switch ($last_num ) {
          case 0:
            $authors['age'] .= ' лет';
            break;
          case 1:
            $authors['age'] .= ' год';
            break;
          case 2:
          case 3:
          case 4:
            $authors['age'] .= ' года';
            break;
          default:
            $authors['age'] .= ' лет';
            break;
        }
      }else{
        $authors['age'] = '';
      }

      $firend = new CRUD('friends');
      
      $str = 'SELECT friends.recipient, friends.requester
        FROM `friends` 
        WHERE friends.recipient='.$id.' OR friends.requester='.$id.' AND friends.status=1';
  
      $needle = array();
      
      $authors['friends'] = $firend->FreeInfo($str,$needle)->Resulting;;
      
      $materials = new CRUD('materials');

      $str = 'SELECT materials.id, materials.title, materials.slug, materials.date,
            materials.categories_id, materials.snippet, materials.author_id,
            materials.photo, materials.see, materials.likes, materials.dislikes,
            sum(case when comments.status = "1" then 1 else 0 end) as comments_count,
            users.name as author
            FROM `materials` 
            LEFT JOIN `users` ON users.id = materials.author_id 
            LEFT JOIN `comments` ON comments.post_id =materials.id
            WHERE materials.author_id=\''.$id.'\'  
            AND `moderated`="1" GROUP BY materials.id LIMIT 4';
        
      $needle = array();
      
      $authors['posts'] = $this->extendPostData($materials->FreeInfo($str,$needle)->Resulting);
      
      return $authors;
    }

    public function getAuthorArticles(int $id, array $data)
    {
      $authors = new CRUD('users');

      $categories = require 'config/nav_pages.php';
      $sql = '';
      $sort = '';
      $sort_url = '';

      if(isset($data['sort']['categories']) && !empty($data['sort']['categories'])){
          $sql .= ' AND `categories_id`=\''.array_search($data['sort']['categories'], $categories).'\'';
          $sort_url .= '&categories='.$data['sort']['categories'];
      }

      if(isset($data['sort']['datesort']) && !empty($data['sort']['datesort'])){
          $sort_url .= '&datesort='.$data['sort']['datesort'];
          $sql .= ' AND materials.date >= DATE_SUB(CURDATE(), INTERVAL '.$data['sort']['datesort'].' DAY)';
          $sort = ' ORDER BY materials.date DESC';
      }
      
      if(isset($data['sort']['popularity'])){
          $sort_url .= '&popularity='. $data['sort']['popularity'];
          $sort = ' ORDER BY materials.likes '. ($data['sort']['popularity'] === 'descending' ? 'DESC' : 'ASC');
      }

      if(isset($data['sort']['comments']) && !empty($data['sort']['comments'])){
          $sort_url .= '&comments='. $data['sort']['comments'];
          $sort = ' ORDER BY comments_count '. ($data['sort']['comments'] === 'descending' ? 'DESC' : 'ASC');;
      }

      if(!$sort){
        $sort = ' ORDER BY materials.date DESC';
      }
      
      $str = 'SELECT users.id, users.name, users.photo,
      sum(case when materials.moderated = "1" then 1 else 0 end) as posts
      FROM `users` 
      LEFT JOIN `materials` ON materials.author_id = users.id 
      WHERE users.id='.$id.$sql.' GROUP BY users.id';
  
      $needle = array();
      $authors_array = $authors->FreeInfo($str,$needle)->Resulting;
      if($authors_array){
        $authors = $authors->FreeInfo($str,$needle)->Resulting[0];
      }else{
        $str = 'SELECT users.id, users.name, users.photo
        FROM `users` 
        WHERE users.id='.$id.' GROUP BY users.id';
        $authors = $authors->FreeInfo($str,$needle)->Resulting[0];
      }
      

      $materials = new CRUD('materials');

      $str = 'SELECT materials.id, materials.title, materials.slug, materials.date,
            materials.categories_id, materials.snippet,materials.author_id,
            materials.photo, materials.see, materials.likes, materials.dislikes,
            sum(case when comments.status = "1" then 1 else 0 end) as comments_count,
            users.name as author
            FROM `materials` 
            LEFT JOIN `users` ON users.id = materials.author_id 
            LEFT JOIN `comments` ON comments.post_id =materials.id
            WHERE materials.author_id=\''.$id.'\'  '.$sql.'
            AND `moderated`="1" GROUP BY materials.id '.$sort.' LIMIT '.$data['offset'].','.$data['limit'];
        
      $needle = array();
      
      $authors['articles'] = $this->extendPostData($materials->FreeInfo($str,$needle)->Resulting);
      
      if(isset( $authors['posts'])){
        $authors['pagination'] = new Paginator(
          $authors['posts'], $data['limit'], $data['page'], $data['url']
        );
      }
      

      return $authors;
    }

}
