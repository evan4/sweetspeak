<?php 

namespace App\Models;

use Mycms\Model;
use Exception;

class Subscriber
{
    private $db;

    private $table = 'subscribers';

    public function __construct()
    {
        $this->db = new Model($this->table);
    }

    public function getSubscriber(array $params = null, array $data)
    {
        return $this->db->select($params, $data);
    }

    public function getSubscribers(array $params = null, array $data = null)
    {
        return $this->db->selectAll($params, $data);
    }

    public function count($field, array $data = null)
    {
        return $this->db->count($field, $data)[0]['total'];
    }

    public function createSubscriber(array $data)
    {
        return $this->db->insert($data);
    }
    
    public function deleteSubscriber(array $data)
    {
        return $this->db->delete($data);
    }
}
