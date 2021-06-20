<?php 

namespace App\Models;

use Mycms\Model;
use Exception;

class Comment
{
    private $db;

    private $table = 'comments';

    public function __construct()
    {
        $this->db = new Model($this->table);
    }

    public function getComment(array $data)
    {
        return $this->db->select($data);
    }

    public function getComments(array $params = null, array $data = null)
    {
        return $this->db->selectAll($params, $data);
    }

    public function count($field, array $data = null)
    {
        return $this->db->count($field, $data)[0]['total'];
    }

    public function createComment(array $data)
    {
        return $this->db->insert($data);
    }

    public function updateComment(array $data)
    {
        return $this->db->update($data);
    }
    
    public function deleteComment(array $data)
    {
        return $this->db->delete($data);
    }
}
