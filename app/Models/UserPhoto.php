<?php 

namespace App\Models;

use Mycms\Model;
use Exception;

class UserPhoto
{
    private $db;

    public function __construct()
    {
        $this->db = new Model('user_photos');
    }

    public function getUserPhotos(array $params = null,array $data)
    {
        return $this->db->selectAll($params,$data);
    }

    public function count($field, array $data = null)
    {
        return $this->db->count($field, $data)[0]['total'];
    }
    
    public function saveUserPhoto(array $data)
    {
        return $this->db->insert($data);
    }
    
    public function deleteUserPhoto(array $data)
    {
        return $this->db->delete($data);
    }
}
