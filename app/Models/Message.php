<?php 

namespace App\Models;

use Mycms\Model;
use Exception;

class Message
{
    private $db;

    private $table = 'messages';

    public function __construct()
    {
        $this->db = new Model($this->table);
    }

    public function getMessage(array $params = null, array $data)
    {
        return $this->db->select($params, $data);
    }

    public function getMessages(array $params = null, array $data = null)
    {
        return $this->db->selectAll($params, $data);
    }

    public function count($field, array $data = null)
    {
        return $this->db->count($field, $data)[0]['total'];
    }

    public function createMessage(array $data)
    {
        return $this->db->insert($data);
    }

    public function updateMessage(array $data)
    {
        return $this->db->update($data);
    }
    
    public function deleteMessage(array $data)
    {
        return $this->db->delete($data);
    }
}
