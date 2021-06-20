<?php 

namespace App\Models;

use Mycms\Model;
use Exception;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Model('users');
    }

    public function getUser(array $params = null,array $data)
    {
        return $this->db->select($params,$data);
    }

    public function getUsers(array $params = null, array $data = null)
    {
        return $this->db->selectAll($params, $data);
    }

    public function count($field, array $data = null)
    {
        return $this->db->count($field, $data)[0]['total'];
    }
    
    public function saveUser(array $data)
    {
        return $this->db->insert($data);
    }

    public function updateUser(array $data)
    {
        return $this->db->update($data);
    }
    
    public function deleteUser(array $data)
    {
        return $this->db->delete($data);
    }
}
