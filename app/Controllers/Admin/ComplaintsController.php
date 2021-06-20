<?php

namespace App\Controllers\Admin;

use Verot\Upload\Upload;
use Symfony\Component\HttpFoundation\Request;

use App\Models\Complaint;

class ComplaintsController extends AdminController
{

  public function index()
  {
    if($this->role === 'user' || $this->role === 'moderator'){
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
        $sort .= " ORDER BY complaints.".$this->sanitizeText($_POST['sortBy'])." ".$order;
      }
    }

    $complains = new Complaint();
    $data = [
      'join' => " LEFT JOIN `users` as us ON complaints.author_id = us.id LEFT JOIN `users` as us1 ON complaints.users_id = us1.id",
      "endQuery" => " GROUP BY complaints.id ".$sort.$limit
    ];
    $res = [
      'data' =>
        $complains->getComplaints(
        ['complaints.id', 'complaints.message', 'complaints.created_at', 
        'complaints.author_id', 'complaints.users_id', "us.name as author", "us1.name as users",
        ],
        $data
      ),
      'total' => $complains->count('id', [])
    ];

    echo json_encode($res);
    die();
  }

  public function delete(Request $request)
  {
    $author_id = (int) $_POST['author_id'];
    if((int) $this->user['id'] !== $author_id){
      if($this->role === 'user' || $this->role === 'moderator'){
        $this->echoForbittenMessage();
      }
    }

    $id = (int) $request->attributes->get('id');
    if($id){
      
      $complaint = new Complaint();
      $res = $complaint->deleteComplaint(
      [
        'complaints.id' => $id
      ]);

      echo json_encode([
        'success' => true,
        'res'=>$res
        ]);
      die();
    }

  }

}
