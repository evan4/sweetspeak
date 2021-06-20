<?php

namespace App\Controllers\Admin;

use Symfony\Component\HttpFoundation\Request;

use App\Models\Friend;

class FriendsController extends AdminController
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
      if($_POST['sortBy'] === 'requester'){
        $sort .= " ORDER BY users.name ".$order;
      }else{
        $sort .= " ORDER BY friends.".$this->sanitizeText($_POST['sortBy'])." ".$order;
      }
    }

    $data = [
    'friends.recipient' => $this->user['id'],
    'friends.status' => '1',
    'join' => "  LEFT JOIN `users` ON friends.requester = users.id",
    "endQuery" => " OR friends.requester = ".$this->user['id']." GROUP BY friends.id ".$sort.$limit
    ];

    $condition = [
        'friends.recipient' => $this->user['id'],
        'friends.status' => '1',
        "endQuery" => " OR friends.requester = ".$this->user['id']
    ];
    
    $friend = new Friend();

    $res = [
      'data' =>
        $friend->getFriends(
        ['friends.id','friends.requester', "users.name as user",
        ],
        $data
      ),
      'total' => $friend->count('id', $condition)
    ];

    echo json_encode($res);
    die();
  }

  public function friendsRequests()
  {
    $data = [
      'friends.status' => 0,
      'friends.recipient' => $this->user['id'],
      'join' => " LEFT JOIN `users` ON friends.requester = users.id",
      "endQuery" => " GROUP BY friends.id "
      ];
      $condition = [
          'friends.status' => 0,
          'friends.recipient' => $this->user['id'],
      ];
      
      $friend = new Friend();
  
      $res = [
        'data' =>
          $friend->getFriends(
          ['friends.id', 'friends.created_at', 'friends.requester', "users.name as user",
          ],
          $data
        ),
        'total' => $friend->count('id', $condition)
      ];
  
      echo json_encode($res);
      die();
  }

  public function accespFriendRequest()
  {
    $friend = new Friend();
    $data = [
    'id' => (int) $_POST['id'],
    'status' => "1"
    ];

    $res = $friend->updateFriend($data);
    echo json_encode(['res'=>$res]);
    die();
  }

  public function delete(Request $request)
  {
    $id = (int) $request->attributes->get('id');

    $friend = new Friend();

    $data = [
      'friends.id' => $id,
    ];

    $res = $friend->deleteFriend($data);
    echo json_encode(['res'=>$res]);
    die();
    
  }
 
}
