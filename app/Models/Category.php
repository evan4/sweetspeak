<?php 

namespace App\Models;

use Mycms\Model;
use Exception;

class Category
{
    private $db;

    private $table = 'categories';

    public function __construct()
    {
        $this->db = new Model($this->table);
    }

    public function getCategory(array $data)
    {
        return $this->db->select($data);
    }

    public function getCategories(array $params = null, array $data = null)
    {
        return $this->db->selectAll($params, $data);
    }

    public function count($field, array $data = null)
    {
        return $this->db->count($field, $data)[0]['total'];
    }

    public function createCategory(array $data)
    {
        return $this->db->insert($data);
    }

    public function updateCategory(array $data)
    {
        return $this->db->update($data);
    }
    
    public function deleteCategory(array $data)
    {
        return $this->db->delete($data);
    }
}
