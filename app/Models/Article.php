<?php 

namespace App\Models;

use Mycms\Model;
use Exception;

class Article
{
    private $db;

    private $table = 'materials';

    public function __construct()
    {
        $this->db = new Model($this->table);
    }

    public function getArticle(array $params = null, array $data)
    {
        return $this->db->select($params, $data);
    }

    public function getArticles(array $params = null, array $data = null)
    {
        return $this->db->selectAll($params, $data);
    }

    public function count($field, array $data = null)
    {
        return $this->db->count($field, $data)[0]['total'];
    }
    
    public function createArticle(array $data)
    {
        return $this->db->insert($data);
    }

    public function updateArticle(array $data)
    {
        return $this->db->update($data);
    }
    
    public function deleteArticle(array $data)
    {
        return $this->db->delete($data);
    }

    public function checkSlugUniqueness( $slug)
    {
      $checkSlugUniqueness = $this->getArticle(
        ['materials.slug'],
        [ 'materials.slug' => $slug ]
      );
  
      if($checkSlugUniqueness){
        echo json_encode([
          'error' => 'Этот адрес статьи уже занят',
          'success' => false
        ]);
        die();
      }else{
        return true;
      }
  
    }
}
