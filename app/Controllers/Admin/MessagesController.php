<?php

namespace App\Controllers\Admin;

use Symfony\Component\HttpFoundation\Request;

use App\Models\Message;

class MessagesController extends AdminController
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
        $sort .= " ORDER BY messages.".$this->sanitizeText($_POST['sortBy'])." ".$order;
      }
    }

    $data = [
    'messages.user_id' => $this->user['id'],
    'join' => " LEFT JOIN `users` ON messages.author_id = users.id",
    "endQuery" => " GROUP BY messages.id ".$sort.$limit
    ];
    $condition = [
        'messages.user_id' => $this->user['id'],
    ];
    
    $message = new Message();

    $res = [
      'data' =>
        $message->getMessages(
        ['messages.id', 'messages.body', 'messages.created_at', 
          "messages.author_id","messages.user_id", "messages.status",
          "users.name as author",
        ],
        $data
      ),
      'total' => $message->count('id', $condition)
    ];

    echo json_encode($res);
    die();
  }

  public function getAllMessages()
  {
    if($this->role != 'superadmin'){
      $this->echoForbittenMessage();
    }

    $page = isset($_POST['page']) ? (int) $_POST['page'] : 1;

    $offset = $page === 1 ? 0 : ($page - 1) * $this->limit;

    $limit = " LIMIT ".$offset.','.$this->limit;

    $sort = '';

    if(isset($_POST['sortBy'])
    && !empty($_POST['sortBy'])){
      $order = $_POST['sortDesc'] === 'true' ? 'DESC' : 'ASC';
      if($_POST['sortBy'] === 'author'){
        $sort .= " ORDER BY author ".$order;
      }else if($_POST['sortBy'] === 'users'){
        $sort .= " ORDER BY users ".$order;
      }else{

        $sort .= " ORDER BY messages.".$this->sanitizeText($_POST['sortBy'])." ".$order;
      }
    }

    $data = [
      'join' => " LEFT JOIN `users` as us ON messages.author_id = us.id LEFT JOIN `users` as us1 ON messages.user_id = us1.id",
      "endQuery" => " GROUP BY messages.id ".$sort.$limit
    ];
    
    $message = new Message();

    $res = [
      'data' =>
        $message->getMessages(
        ['messages.id', 'messages.body', 'messages.created_at', 
          "messages.author_id","messages.user_id", "messages.status",
          "us.name as author", "us1.name as users",
        ],
        $data
      ),
      'total' => $message->count('id', [])
    ];

    echo json_encode($res);
    die();
  }

  public function create()
  {
    $message = new Message();
    $data = [
        'messages.body' => $this->sanitizeText($_POST['body']),
        'messages.author_id' => $this->user['id'],
        'messages.user_id' => intval($_POST['user_id']),
    ];
    
    $res = $message->createMessage($data);

    echo json_encode($res);
    die();
  }

  public function readMessage()
  {
    $message = new Message();
    $data = [
    'id' => (int) $_POST['id'],
    'status' => "1"
    ];

    $res = $message->updateMessage($data);
    echo json_encode(['res'=>$res]);
    die();
  }

  public function authorMessages()
  {
    $message = new Message();
    $data = [
      'messages.author_id' => (int) $_POST['author_id'],
      'messages.user_id' => (int) $this->user['id'],
      'join' => " LEFT JOIN `users` ON messages.author_id = users.id",
      "endQuery" => " ORDER BY messages.created_at"
    ];

    $messagesToUser = [
      'data' =>
        $message->getMessages(
        ['messages.id', 'messages.body', 'messages.created_at', "messages.status",
        "messages.author_id","messages.user_id", "users.name as author",
        ],
        $data
      )
    ];

    $data = [
      'messages.author_id' => (int) $this->user['id'],
      'messages.user_id' => (int) $_POST['author_id'],
      'join' => " LEFT JOIN `users` ON messages.author_id = users.id",
      "endQuery" => " ORDER BY messages.created_at"
    ];

    $messagesFromuser = [
      'data' =>
        $message->getMessages(
        ['messages.id', 'messages.body', 'messages.created_at', "messages.status",
        "messages.author_id","messages.user_id", "users.name as author",
        ],
        $data
      )
    ];

    $messages = [];

    for ($i=0; $i < count($messagesToUser['data']); $i++) { 
      $messages[] = $messagesToUser['data'][$i];
    }
    for ($i=0; $i < count($messagesFromuser['data']); $i++) { 
      $messages[] = $messagesFromuser['data'][$i];
    }
   
    usort($messages, function($a, $b) {
      //return strtotime($a['start_date']) - strtotime($b['start_date']);
      return strcmp($a['created_at'], $b['created_at']);
   });

    echo json_encode($messages);
    die();

  }

  public function delete(Request $request)
  {
    $id = (int) $request->attributes->get('id');

    $message = new Message();

    $data = [
      'messages.id' => $id,
    ];

    $msg = $message->getMessage(
        ['messages.id', "messages.author_id", "messages.user_id"],
        $data);
    
    if($this->admin || 
    $msg['author_id'] === $this->user['id'] ||
    $msg['user_id'] === $this->user['id']  ){

      $res = $message->deleteMessage($data);
      echo json_encode(['res'=>$res]);
      die();
    }

    $this->echoForbittenMessage();
  }
 
}
