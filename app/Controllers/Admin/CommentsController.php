<?php

namespace App\Controllers\Admin;

use Verot\Upload\Upload;
use Symfony\Component\HttpFoundation\Request;

use App\Models\Comment;
use App\Models\CommentPhotos;

class CommentsController extends AdminController
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
        $sort .= " ORDER BY comments.".$this->sanitizeText($_POST['sortBy'])." ".$order;
      }
    }

    if($this->role !== 'user'){
      $data = [
        'join' => " LEFT JOIN `users` ON comments.author_id = users.id
        LEFT JOIN `materials` ON comments.post_id = materials.id
        ",
        "endQuery" => " GROUP BY comments.id ".$sort.$limit
      ];
      $condition = [];
    }else{
      $data = [
        'comments.author_id' => $this->user['id'],
        'join' => " LEFT JOIN `users` ON comments.author_id = users.id LEFT JOIN `materials` ON comments.post_id = materials.id",
        "endQuery" => " GROUP BY comments.id ".$sort.$limit
      ];
      $condition = ['comments.author_id' => $this->user['id']];
    }
    $comment = new Comment();

    $res = [
      'data' =>
        $comment->getComments(
        ['comments.id', 'comments.body', 'comments.parent','comments.created_at', "comments.post_id",
          "comments.author_id","comments.status","users.name as author", 
          "materials.snippet", "materials.title","materials.slug", "materials.categories_id"
        ],
        $data
      ),
      'total' => $comment->count('id', $condition)
    ];

    if(count($res['data'])> 0){
      $comment_photos = new CommentPhotos();
      for ($i=0; $i < count($res['data']); $i++) { 
        
        $res['data'][$i]['url'] = '/article/'.$res['data'][$i]['slug'];
        $res['data'][$i]['snippet'] = strip_tags($res['data'][$i]['snippet']);
        $photos = $comment_photos->getPhotos(
          ['comment_photos.photo'],
          [
            'comment_photos.comment_id' => $res['data'][$i]['id'],
          ]
        );
        $res['data'][$i]['photos'] = $photos ? $photos : [];
        
      }
     
    }
    echo json_encode($res);
    die();
  }

  public function getAnswers()
  {
    $comment = new Comment();
    if(isset($_POST['id']) ){
      $id = (int) $_POST['id'];
      $data = [
        'comments.parent' => $id,
        'join' => " LEFT JOIN `users` ON comments.author_id = users.id
        ",
        "endQuery" => " GROUP BY comments.id "
      ];
      
      $res = [
        'data' =>
          $comment->getComments(
          ['comments.id', 'comments.body', 'comments.parent','comments.created_at', "comments.post_id",
            "comments.author_id","comments.status","users.name as author",
          ],
          $data
        )
      ];

    if(count($res['data'])> 0){
      $comment_photos = new CommentPhotos();
      for ($i=0; $i < count($res['data']); $i++) { 

        $photos = $comment_photos->getPhotos(
          ['comment_photos.photo'],
          [
            'comment_photos.comment_id' => $res['data'][$i]['id'],
          ]
        );
        $res['data'][$i]['photos'] = $photos ? $photos : [];
        
      }
     
    }
    echo json_encode($res);
    die();

    }
  }

  public function create()
  {
    $comment = new Comment();
    $data = [
      'comments.body' => $this->sanitizeText($_POST['body']),
      'comments.parent' => isset($_POST['parent']) ? (int) $_POST['parent'] : 0,
      'comments.post_id' => (int) $_POST['post_id'],
      'comments.author_id' => $this->user['id'],
      'comments.status' => "1"
    ];
    
    $res = $comment->createComment($data);

    if(isset($_POST['photos'])){
      $photos = explode('data:image/', $_POST['photos']) ?? null;
      array_shift($photos);
    
      if($res && isset($photos)){
        
        $commentPhotos = new CommentPhotos();
        
        for ($i=0; $i < count($photos); $i++) { 

          
          $data = 'data:image/'.$photos[$i];
          list($type, $data) = explode(';', $data);
          $ext = explode('/', $type)[1];
          
          $this->checkImageExtension($ext);

          list(, $data) = explode(',', $data);
          $data = base64_decode($data);

          $photo = strtotime("now").$i.'.'.$ext;;
          $img = BUNDLES.'comments/'.$photo;
          file_put_contents($img, $data);

          $commentPhotos->addPhoto([
            'comment_photos.comment_id' => (int) $res['id'],
            'comment_photos.photo' => $photo,
          ]);
        }
      }
    }
    echo json_encode($res);
    die();
  }

  public function approve()
  {

    if($this->role !== 'user'){
      $comment = new Comment();
      $data = [
        'id' => (int) $_POST['id'],
        'status' => "1"
      ];

      $res = $comment->updateComment($data);
      echo json_encode(['res'=>$res]);
      die();
    }

    $this->echoForbittenMessage();
  }

  public function delete(Request $request)
  {
    if($this->role !== 'user'){
      $id = (int) $request->attributes->get('id');

      $comment = new Comment();

      $data = [
        'comments.id' => $id,
      ];

      $commentPhotos = new CommentPhotos();
      $photos = $commentPhotos->getPhotos(
        ['comment_photos.photo', 
        ],
        [
          'comment_photos.comment_id' => $id,
        ]
      );
      
      if(count($photos) > 0){
        foreach($photos as $photo){
          $this->deleteImage($photo['photo'], 'comments');
        }
      }

      $comment_current = $comment->getComment([
        'comment.parent'
      ],$data);

      if($comment_current['parent'] === 0){
        $comments = $comment->getComments([
          'comment.id'
          ],
        [
          'comments.parent' => $id,
        ]);

        foreach ($comments as $comment) {

          $photos = $commentPhotos->getPhotos(
            ['comment_photos.photo'],
            [
              'comment_photos.comment_id' => $comment['id'],
            ]
          );
          
          if(count($photos) > 0){
            foreach($photos as $photo){
              $this->deleteImage($photo['photo'], 'comments');
            }
          }

          $comment->deleteComment([
            'comments.id' => $comment['id'],
          ]);
        }
      }

      $res = $comment->deleteComment($data);
      echo json_encode(['res'=>$res]);
      die();
    }

    $this->echoForbittenMessage();
  }
 
}
