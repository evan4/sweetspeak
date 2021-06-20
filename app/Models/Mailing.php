<?php 

namespace App\Models;

use Mycms\Model;
use Exception;

class Mailing
{
    private $db;

    private $table = 'mailings';

    public function __construct()
    {
        $this->db = new Model($this->table);
    }

    public function getTemplate(array $params = null, array $data)
    {
        return $this->db->select($params, $data);
    }

    public function getTemplates(array $params = null, array $data = null)
    {
        return $this->db->selectAll($params, $data);
    }

    public function count($field, array $data = null)
    {
        return $this->db->count($field, $data)[0]['total'];
    }

    public function createTemplate(array $data)
    {
        return $this->db->insert($data);
    }

    public function updateTemplate(array $data)
    {
        return $this->db->update($data);
    }
    
    public function deleteTemplate(array $data)
    {
        return $this->db->delete($data);
    }
}
