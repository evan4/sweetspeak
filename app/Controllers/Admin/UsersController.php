<?php

namespace App\Controllers\Admin;

use Verot\Upload\Upload;

use App\Models\User;
use App\Models\UserPhoto;
use App\Models\Article;
use App\Models\Comment;
use App\Models\CommentPhotos;

class UsersController extends AdminController
{

  public function index()
  {
    if($this->role === 'user'){
      $this->echoForbittenMessage();
    }

    $page = isset($_POST['page']) ? (int) $_POST['page'] : 1;

    $offset = $page === 1 ? 0 : ($page - 1) * $this->limit;

    $limit = " LIMIT ".$offset.','.$this->limit;

    $sort = '';

    if(isset($_POST['sortBy'])
    && !empty($_POST['sortBy'])){
      $order = $_POST['sortDesc'] === 'true' ? 'DESC' : 'ASC';
      $sort .= " ORDER BY users.".$this->sanitizeText($_POST['sortBy'])." ".$order;
    }

    $user = new User();
    $data = [
      'users.id' => $this->user['id'],
      "condition" => [
        "users.id" => '<>'
      ],
      "endQuery" => " AND users.status <> 'superadmin' ".$sort.$limit
    ];
    $condition = [
      'users.id' => $this->user['id'],
      "condition" => [
        "users.id" => '<>'
      ],
    ];

    $res = [
      'data' =>
        $user->getUsers(
        ['users.id',' users.name', 'users.email',
        'users.nickname', 'users.status', 'users.photo','users.avatar',
        'users.balance', 'users.author_points', 'users.created_at', 
        'users.userstatus', 'users.motto', 'users.bio', 'users.gender', 'users.target',
        'users.age', 'users.city', 'users.instagram', 'users.	vk', 'users.facebook',
        'users.website', 'users.twitter',
        ],
        $data
      ),
      'total' => $user->count('id', $condition)
    ];
    echo json_encode($res);
    die();
  }

  public function current()
  {
    if($this->role === 'user'){
      $this->echoForbittenMessage();
    }

    $id = (int) $_POST['id'];

    $user = new User();

    $res = $user->getUser(
      ['users.id',' users.name', 'users.email','users.confirm',
        'users.nickname', 'users.status', 'users.photo','users.avatar',
        'users.balance', 'users.author_points', 'users.created_at', 
        'users.userstatus', 'users.motto', 'users.bio', 'users.gender', 'users.target',
        'users.age', 'users.city', 'users.instagram', 'users.	vk', 'users.facebook',
        'users.website', 'users.twitter',            
      ],
      [ 
          'users.id' => $id,
          'join' => " LEFT JOIN `materials` ON materials.author_id = users.id LEFT JOIN `comments` ON comments.author_id = users.id",
          'sum' => ", sum(case when materials.author_id = ".$id ." then 1 else 0 end) as articles,
                sum(case when comments.author_id = ".$id ." then 1 else 0 end) as comments",
      ]
    );

    if($res['status'] == 'superadmin' &&  $this->role != 'superadmin'){
      $this->echoForbittenMessage();
    }

    $userPhotos = new UserPhoto();

    $res['photos'] = $userPhotos->getUserPhotos(
    ['user_photos.id', 'user_photos.photo'],
    ['user_photos.author_id' => $id]);
                
    echo json_encode(["user" => $res] );
    die();
  }

  public function addPhhoto()
  {
    
    if(isset($_FILES['photo']) && $_FILES['photo']['name']!=""){
      
      $data['user_photos.author_id'] = (int) $this->user['id'];

      $userPhotos = new UserPhoto();
      $totalPhotos = $userPhotos->count('id',$data);
      
      if($totalPhotos > 9){
        $res['success'] = false;
        $res['error'] = 'Максимальное количество фотографий не должно превышать 10 штук';
        echo json_encode($res);
        die();
      }

      $handle = new Upload($_FILES['photo']);

      $this->checkImageExtension($handle->file_src_name_ext);

      $strtotime = strtotime("now");
      
      $data['user_photos.photo'] = $strtotime.'.webp';
      
      if ($handle->uploaded) {
        for ($i=0; $i < count($this->imageSize); $i++) { 
          $handle->image_convert = 'webp';
          $handle->file_new_name_body   = $this->imageSize[$i].'_'.$strtotime;
          $handle->image_resize         = true;
          $handle->image_x              = $this->imageSize[$i];
          $handle->image_ratio_y        = true;
          $handle->process(BUNDLES.'users/');
          $handle->processed;
      
        }
      $handle->clean();

      $res = $userPhotos->saveUserPhoto($data);
      
      if($res) {
        $result['id'] = $res['id'];
        $result['photo'] = $data['user_photos.photo'];
        $result['success'] = true;
        echo json_encode($result);
        die();
      }

      $result['success'] = false;
      echo json_encode($result);
      die();
    }
    }
  }

  public function removePhhoto()
  {
    $author_id = (int) $_POST['author_id'];
      
    if(!$this->admin && $this->user['id'] != $author_id ){
      $this->echoForbittenMessage();
    }

    $id = (int)$_POST['id'];
    
    if($id){
      $userPhotos = new UserPhoto();
      
      $res = $userPhotos->deleteUserPhoto(['user_photos.id'=>$id]);

      $this->deleteImages($_POST['photo'], 'users');

      echo json_encode([
        'success' => true
      ]);
      die();
    }

    $result['success'] = false;
    echo json_encode($result);
    die();
  }
  
  public function update()
  {
    $author_id = (int) $_POST['id'];
    
    if((int) $this->user['id'] !== $author_id){
      if($this->role === 'user' || $this->role === 'moderator'){
        $this->echoForbittenMessage();
      }
    }

    $user = new User();

    $args = array(
      'id'   => [
        'filter' => FILTER_VALIDATE_INT,
        'flags' => FILTER_NULL_ON_FAILURE, 
      ],
      'name'  => [
        'filter' => FILTER_SANITIZE_STRING,
        'flags' => FILTER_NULL_ON_FAILURE, 
      ],
      'nickname'  => [
        'filter' => FILTER_SANITIZE_STRING,
        'flags' => FILTER_NULL_ON_FAILURE, 
      ],
      'email'  => [
        'filter' => FILTER_VALIDATE_EMAIL,
        'flags' => FILTER_NULL_ON_FAILURE, 
      ],
      'age'  => [
        'filter'    => FILTER_VALIDATE_INT,
        'flags' => FILTER_NULL_ON_FAILURE, 
        'options'   => array('min_range' => 16, 'max_range' => 90)
      ],
      'target'  => [
        'filter' => FILTER_SANITIZE_STRING,
        'flags' => FILTER_NULL_ON_FAILURE, 
      ],
      'gender'  => [
        'filter' => FILTER_SANITIZE_STRING,
        'flags' => FILTER_NULL_ON_FAILURE, 
      ],
      'status'  => [
        'filter' => FILTER_SANITIZE_STRING,
        'flags' => FILTER_NULL_ON_FAILURE, 
      ],
      'motto'  => [
        'filter' => FILTER_SANITIZE_STRING,
        'flags' => FILTER_NULL_ON_FAILURE, 
      ],
      'bio'  => [
        'filter' => FILTER_SANITIZE_STRING,
        'flags' => FILTER_NULL_ON_FAILURE, 
      ],
      'instagram'  => [
        'filter' => FILTER_SANITIZE_STRING,
        'flags' => FILTER_NULL_ON_FAILURE, 
      ],
      'city'  => [
        'filter' => FILTER_SANITIZE_STRING,
        'flags' => FILTER_NULL_ON_FAILURE, 
      ],
      'vk'  => [
        'filter' => FILTER_SANITIZE_STRING,
        'flags' => FILTER_NULL_ON_FAILURE, 
      ],
      'facebook'  => [
        'filter' => FILTER_SANITIZE_STRING,
        'flags' => FILTER_NULL_ON_FAILURE, 
      ],
      'twitter'  => [
        'filter' => FILTER_SANITIZE_STRING,
        'flags' => FILTER_NULL_ON_FAILURE, 
      ],
      'website'  => [
        'filter' => FILTER_VALIDATE_URL,
        'flags' => FILTER_NULL_ON_FAILURE, 
      ],
    );

    $params = filter_input_array(INPUT_POST, $args, true);
    
    if(isset($params['password']) && !empty($params['password'])){
      unset($params['password']);
    }

    if(isset($_FILES['photo']) && $_FILES['photo']['name']!=""){

      $res = $user->getUser(
        ['users.id','users.photo',           
        ],
        [ 
          'users.id' => $this->user['id']
        ]
      );

      if($res['photo']){
        $this->deleteImages($res['photo'], 'users');
      }

      $handle = new Upload($_FILES['photo']);
      $strtotime = strtotime("now");
      $params['photo'] = $strtotime.'.webp';
      
      $this->checkImageExtension($handle->file_src_name_ext);

      if ($handle->uploaded) {
        for ($i=0; $i < count($this->imageSize); $i++) { 
          $handle->image_convert = 'webp';
          $handle->file_new_name_body   = $this->imageSize[$i].'_'.$strtotime;
          $handle->image_resize         = true;
          $handle->image_x              = $this->imageSize[$i];
          $handle->image_ratio_y        = true;
          $handle->process(BUNDLES.'users/');
          $handle->processed;
          
        }
        $handle->clean();
      }

      $res = $user->updateUser($params);
    }else{
      $res = $user->updateUser($params);
    }

    echo json_encode(['res'=>$res]);
    die();
  }

  public function changeUserVisibility()
  {
    if($this->role === 'user'){
      $this->echoForbittenMessage();
    }
    $id = (int) $_POST['id'];
    $confirm = (int) $_POST['confirm'];
    $user = new User();

    $data = [
      'id' => $id,
      'confirm' => $confirm
    ];

    $res = $user->updateUser($data);
    echo json_encode(['res'=>$res]);
    die();

  }

  public function delete()
  {
    $author_id = (int) $_POST['author_id'];

    if((int) $this->user['id'] !== $author_id){
      if($this->role === 'user' || $this->role === 'moderator'){
        $this->echoForbittenMessage();
      }
    }

    $id = (int)$_POST['id'];
    if($id){
      
      $user = new User();

      $this->deleteImages($_POST['photo'], 'users');

      $userPhotos = new UserPhoto();
      
      $photos = $userPhotos->getUserPhotos(
        ['user_photos.id', 'user_photos.photo'],
        ['user_photos.author_id' => $id]);
        
      foreach ($photos as $key => $value) {
        $this->deleteImages($value['photo'], 'users');
      }

      $userPhotos->deleteUserPhoto(['user_photos.author_id'=>$id]);
      
      $articles = new Article();
      $users_articles = $articles->getArticles(
        ['materials.id','materials.photo'],
        ['materials.author_id' => $id]
      );
      
      $post_ids = ['comments.author_id' => $id];

      foreach ($users_articles as $users_article) {
        $this->deleteImages($users_article['photo'], 'articles');
        $post_ids +=['comments.post_id' => $users_article['id']];
      }
      $comments = new Comment();
      
      $comments = $comments->getComments(
        ['comments.id'],
        $post_ids
      );
      
      $data = [];
      foreach ($comments as $comment) {
        $data += ['comment_photos.comment_id' => $comment['id']];
      }
      
      $commentsPhotos = new CommentPhotos();
      $photos = $commentsPhotos->getPhotos(
        ['comment_photos.photo'],
        $data
      );
      
      foreach ($photos as $photo) {
        $this->deleteImage($photo['photo'], 'comments');
      }

      $res = $user->deleteUser(['users.id'=>$id]);

      echo json_encode([
        'success' => true,
        'res'=>$res
        ]);
      die();
    }

  }

}
