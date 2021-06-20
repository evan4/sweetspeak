<?php 

namespace App\Models;

use Mycms\Model;
use Exception;

class Friend
{
    private $db;

    private $table = 'friends';

    public function __construct()
    {
        $this->db = new Model($this->table);
    }

    public function getfriend(array $params = null, array $data)
    {
        return $this->db->select($params, $data);
    }

    public function getFriends(array $params = null, array $data = null)
    {
        return $this->db->selectAll($params, $data);
    }

    public function count($field, array $data = null)
    {
        return $this->db->count($field, $data)[0]['total'];
    }

    public function addFriend(array $data)
    {
        return $this->db->insert($data);
    }

    public function updateFriend(array $data)
    {
        return $this->db->update($data);
    }
    
    public function deleteFriend(array $data)
    {
        return $this->db->delete($data);
    }
}
