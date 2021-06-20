<?php 

namespace App\Models;

use Mycms\Model;
use Exception;

class CommentPhotos
{
    private $db;

    private $table = 'comment_photos';

    public function __construct()
    {
        $this->db = new Model($this->table);
    }

    public function getPhotos(array $params = null, array $data = null)
    {
        return $this->db->selectAll($params, $data);
    }

    public function count($field, array $data = null)
    {
        return $this->db->count($field, $data)[0]['total'];
    }

    public function addPhoto(array $data)
    {
        return $this->db->insert($data);
    }
    
    public function deletePhoto(array $data)
    {
        return $this->db->delete($data);
    }
}
