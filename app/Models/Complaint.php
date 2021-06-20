<?php 

namespace App\Models;

use Mycms\Model;
use Exception;

class Complaint
{
    private $db;

    private $table = 'complaints';

    public function __construct()
    {
        $this->db = new Model($this->table);
    }

    public function getComplaint(array $data)
    {
        return $this->db->select($data);
    }

    public function getComplaints(array $params = null, array $data = null)
    {
        return $this->db->selectAll($params, $data);
    }

    public function count($field, array $data = null)
    {
        return $this->db->count($field, $data)[0]['total'];
    }

    public function createComplaint(array $data)
    {
        return $this->db->insert($data);
    }

    public function deleteComplaint(array $data)
    {
        return $this->db->delete($data);
    }
}

