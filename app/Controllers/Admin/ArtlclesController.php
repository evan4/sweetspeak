<?php

namespace App\Controllers\Admin;

use Verot\Upload\Upload;

use App\Models\Article;
use models\IndexModel;

class ArtlclesController extends AdminController
{
  public function index()
  {
    $page = isset($_POST['page']) ? (int) $_POST['page'] : 1;

    $offset = $page === 1 ? 0 : ($page - 1) * $this->limit;

    $limit = " LIMIT ".$offset.','.$this->limit;

    $sort = '';

    if(isset($_POST['sortBy'])
    && !empty($_POST['sortBy'])){
      $order = $_POST['sortDesc'] === 'true' ? 'DESC' : 'ASC';
      if($_POST['sortBy'] === 'author'){
        $sort .= " ORDER BY users.name ".$order;
      }else{
        $sort .= " ORDER BY materials.".$this->sanitizeText($_POST['sortBy'])." ".$order;
      }
    }

    $article = new Article();
    
    if($this->role != 'user'){
      $data = [
        'join' => " LEFT JOIN `users` ON materials.author_id = users.id LEFT JOIN `comments` ON comments.post_id =materials.id",
        'sum' => ", sum(case when comments.status = '1' then 1 else 0 end) as comments_count",
        "endQuery" => " GROUP BY materials.id ".$sort.$limit
      ];
      $condition = [];
    }else{
      $data = [
        'materials.author_id' => $this->user['id'],
        'join' => " LEFT JOIN `users` ON materials.author_id = users.id LEFT JOIN `comments` ON comments.post_id =materials.id",
        'sum' => ", sum(case when comments.status = '1' then 1 else 0 end) as comments_count",
        "endQuery" => " GROUP BY materials.id ".$sort.$limit
      ];
      $condition = ['materials.author_id' => $this->user['id']];
    }

    $res = [
      'data' =>
        $article->getArticles(
        ['materials.id','materials.title', 'materials.titleseo', 'materials.description', 'materials.date',
        'materials.categories_id', 'materials.moderated',
        'materials.photo','materials.author_id', "users.name as author" 
        ],
        $data
      ),
      'total' => $article->count('materials.id', $condition)
    ];
   $all_categories = $_SESSION['all_categories'];
    
    for ($i=0; $i < count($res['data']); $i++) { 
      $id = array_search($res['data'][$i]['categories_id'], array_column($all_categories['all'], 'id'));
      $res['data'][$i]['subcategory'] = $all_categories['all'][$id]['name'];
      $res['data'][$i]['subcategory_slug'] = $all_categories['all'][$id]['slug'];
      $parent_id =$all_categories['all'][$id]['parent'] - 1;
      
      $res['data'][$i]['category'] = $all_categories['all'][$parent_id]['name'];
      $res['data'][$i]['category_slug'] = $all_categories['all'][$parent_id]['slug'];

    }

    echo json_encode($res);
    die();
  }

  public function getArticle()
  {
    $article = new Article();
    $id = (int) $_GET['id'];
 
    $res = $article->getArticle(
      ['materials.id', 'materials.title','materials.titleseo', 'materials.description', 
      'materials.slug', 'materials.date',
      'materials.categories_id', 'materials.snippet',
      'materials.content', 'materials.moderated',
      'materials.photo', 'materials.author_id'
      ],[
        'materials.id' => $id
      ]);
    if((int) $this->user['id'] !== $res['author_id']){
      if($this->role === 'user' || $this->role === 'moderator'){
        $this->echoForbittenMessage();
      }
    }
    echo json_encode($res);
    die();
  }

  public function create()
  {

    if(!isset($_SESSION['User_info']['confirm']) || $_SESSION['User_info']['confirm'] != 1){
      $this->echoForbittenMessage();
    }
    
    $article = new Article();
    $title = $this->sanitizeText(strip_tags(trim($_POST['title'])));
    if(isset($_POST['slug']) && !empty($_POST['slug'])){
      $slug = $this->sanitizeText(strip_tags(trim($_POST['slug'])));
      $slug = slug($slug, '-');
    }else{
      $slug = slug($title, '-');
    }

    $article->checkSlugUniqueness($slug);
    
    $data = [
      'materials.title' =>  $title,
      'materials.slug' => $slug,
      'materials.titleseo' => (isset($_POST['titleseo']) && !empty($_POST['titleseo'])) ? $this->sanitizeText($_POST['titleseo']) : $title,
      'materials.description' => (isset($_POST['description']) && !empty($_POST['description'])) ? $this->sanitizeText($_POST['description']) : $title,
      'materials.content' => $_POST['content'],
      'materials.snippet' => mb_substr(strip_tags($_POST['snippet']), 0, 150)."… ",
      'materials.categories_id' => (int )$_POST['categories_id'],
      'materials.author_id' => $this->user['id'],
      'materials.moderated' => "1"
    ];
    
    if(isset($_FILES['photo']) && !empty($_FILES['photo']['name'])){

      $handle = new Upload($_FILES['photo']);
      $strtotime = strtotime("now");
      $data['materials.photo'] = $strtotime.'.webp';
      
      $this->checkImageExtension($handle->file_src_name_ext);

      if ($handle->uploaded) {
        for ($i=0; $i < count($this->imageSize); $i++) { 
          $handle->file_new_name_body   = $this->imageSize[$i].'_'.$strtotime;
          $handle->image_resize         = true;
          $handle->image_x              = $this->imageSize[$i];
          $handle->image_ratio_y        = true;
          $handle->image_convert = 'webp';
          $handle->process(BUNDLES.'articles/');
          $handle->processed;
          
        }
        $handle->clean();
      }
      $res = $article->createArticle($data);
      $this->refreshArticles();
    }else{
      $res = ['error'=> 'Нужно добавить фото'];
    }

    echo json_encode($res);
    die();
  }

  public function updateStatus()
  {
    $author_id = (int) $_POST['author_id'];
    
    if((int) $this->user['id'] !== $author_id){
      if($this->role === 'user'){
        $this->echoForbittenMessage();
      }
    }
    
    $id = (int) $_POST['id'];
    $status = (int) $_POST['moderated'];

    $data = [
      'id' => $id,
      'moderated' => $status
    ];
   
    $article = new Article();
    $res = $article->updateArticle($data);
    $this->refreshArticles();
    echo json_encode(['res'=>$res]);
    die();

  }

  public function update()
  {
    $author_id = (int) $_POST['author_id'];
    
    if((int) $this->user['id'] !== $author_id){
      if($this->role === 'user' || $this->role === 'moderator'){
        $this->echoForbittenMessage();
      }
    }

    $id = (int) $_POST['id'];
    $article = new Article();
    $title = $this->sanitizeText(strip_tags(trim($_POST['title'])));
    if(isset($_POST['slug']) && !empty($_POST['slug'])){
      $slug = $this->sanitizeText(strip_tags(trim($_POST['slug'])));
      $slug = slug($slug, '-');
    }else{
      $slug = slug($title, '-');
    }

    $articleCurrent = $article->getArticle([
      'materials.slug'
    ],
    ['materials.id' => $id]);

    if($articleCurrent && 
    $articleCurrent['slug'] != $slug){
      $article->checkSlugUniqueness($slug);
    }
    if(!$articleCurrent){
      $article->checkSlugUniqueness($slug);
    }

    $data = [
      'id' => $id,
      'title' => $title,
      'slug' => $slug,
      'titleseo' => (isset($_POST['titleseo']) && !empty($_POST['titleseo'])) ? $this->sanitizeText($_POST['titleseo']) : $title,
      'description' => (isset($_POST['description']) && !empty($_POST['description'])) ? $this->sanitizeText($_POST['description']) : $name,
      'content' => $_POST['content'],
      'snippet' => mb_substr(strip_tags($_POST['snippet']), 0, 150)."… ",
      'categories_id' => (int) $_POST['categories_id']
    ];

    if(isset($_FILES['photo']) && $_FILES['photo']['name']!=""){
      
      $this->deleteImages($_POST['photoOld'], 'articles/');

      $handle = new Upload($_FILES['photo']);
      $strtotime = strtotime("now");
      $data['photo'] = $strtotime.'.webp';

      $this->checkImageExtension($handle->file_src_name_ext);
      
      if ($handle->uploaded) {

        for ($i=0; $i < count($this->imageSize); $i++) { 
          $handle->file_new_name_body   = $this->imageSize[$i].'_'.$strtotime;
          $handle->image_resize         = true;
          $handle->image_x              = $this->imageSize[$i];
          $handle->image_ratio_y        = true;
          $handle->image_convert = 'webp';
          $handle->process(BUNDLES.'articles/');
          $handle->processed;
          
        }
        
        $handle->clean();
      }

    }

    $res = $article->updateArticle($data);
    $this->refreshArticles();
    echo json_encode(['res'=>$res]);
    die();
  }

  public function delete()
  {
    
    $author_id = (int) $_POST['author_id'];
    
    if((int) $this->user['id'] !== $author_id){
      if($this->role === 'user'){
        $this->echoForbittenMessage();
      }
    }

    $id = (int)$_POST['id'];

    if($id){
      $article = new Article();
      
      $res = $article->deleteArticle(['materials.id'=>$id]);
      $this->deleteImages($_POST['photo'], 'articles');
      $this->refreshArticles();
      echo json_encode(['res'=> true]);
      die();
    }
  }

  public function refreshArticles()
  {
    $model = new IndexModel();
    
    $_SESSION['popularPosts'] = $model->popularPosts(6);
    
  }

}
