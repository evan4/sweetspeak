<?php

namespace App\Controllers\Admin;

use Verot\Upload\Upload;
use Symfony\Component\HttpFoundation\Request;
use App\Models\Category;
use App\Models\Article;

class CategoriesController extends AdminController
{

  public function index()
  {
    $res = $_SESSION['all_categories'];
    echo json_encode($res);
    die();
  }

  public function create()
  {
    if($this->role === 'user'){
      $this->echoForbittenMessage();
    }

    $category = new Category();
    $parent_id = (int) $_POST['parent'];
    $name = $this->sanitizeText($_POST['name']);
    $data = [
      'categories.name' => $name,
      'categories.slug' => !empty($_POST['slug']) ? $this->sanitizeText($_POST['slug'] ) : slug($this->sanitizeText($_POST['name'] ), '_'),
      'categories.title' => (isset($_POST['title']) && !empty($_POST['title'])) ? $this->sanitizeText($_POST['title']) : $name,
      'categories.description' => (isset($_POST['description']) && !empty($_POST['description'])) ? $this->sanitizeText($_POST['description']) : $name,
      'categories.parent' => $parent_id
    ];

    if(isset($_FILES['photo']) && $_FILES['photo']['name']!=""){

      $handle = new Upload($_FILES['photo']);
      $strtotime = strtotime("now");
      $data['categories.photo'] = $strtotime.'.webp';

      $this->checkImageExtension($handle->file_src_name_ext);
      
      if ($handle->uploaded) {
        $handle->image_convert = 'webp';

        $handle->file_new_name_body = $strtotime;
        
        $handle->process(BUNDLES.'categories/');
        $handle->processed;
        
        $handle->clean();
      }

    }
    
    $res = $category->createCategory($data);
    $this->refreshCategories($parent_id);
    echo json_encode(['res'=>$res]);
    die();
  }

  public function update()
  {
    if($this->role === 'user'){
      $this->echoForbittenMessage();
    }

    $category = new Category();
    $name = $this->sanitizeText($_POST['name']);
    $data = [
      'id' => (int) $_POST['id'],
      'name' => $name,
      'slug' => $_POST['slug'] ? $this->sanitizeText($_POST['slug'] ) : slug($this->sanitizeText($_POST['slug'] )),
      'title' => (isset($_POST['title']) && !empty($_POST['title'])) ? $this->sanitizeText($_POST['title']) : $name,
      'description' => (isset($_POST['description']) && !empty($_POST['description'])) ? $this->sanitizeText($_POST['description']) : $name,
    ];
    
    $all_categories = $_SESSION['all_categories'];
    
    $category_id_in_session = array_search($data['id'], array_column($all_categories['all'], 'id'));

    $category_info = $all_categories['all'][ $category_id_in_session];
 
    if($category_info['photo']){
      $this->deleteImage($category_info['photo'], 'categories');
    }

    $data['parent'] = (int) $category_info['parent'];
    $parent_id = $data['parent'];
    
    if(isset($_FILES['photo']) && $_FILES['photo']['name']!=""){

      $handle = new Upload($_FILES['photo']);
      $strtotime = strtotime("now");
      $data['photo'] = $strtotime.'.webp';

      $this->checkImageExtension($handle->file_src_name_ext);
      
      if ($handle->uploaded) {
        $handle->image_convert = 'webp';

        $handle->file_new_name_body   = $strtotime;
        
        $handle->process(BUNDLES.'categories/');
        $handle->processed;
        
        $handle->clean();
      }

    }
    
    $res = $category->updateCategory($data);
    $this->refreshCategories($parent_id);

    echo json_encode(['res'=>$res]);
    die();
  }
  
  public function refreshCategories(int $parent_id)
  {
    $category = new Category();
    $categories_row = $category->getCategories([], []);

    $subcategories = [];
    $parents = [];
    $categories = [];

    foreach($categories_row as $category){
      if($category['parent'] == 0){
        $categories[$category['name']] = $category['slug'];
        $parents[$category['id']] = $category['slug'];
      }else{
        $subcategories[$parents[$category['parent']]][$category['name']] = $category['slug'];
      }
    }

    if($parent_id === 0){
      $categoires_toString = implode("|", $categories);
      $categoires_toString = 'category_slug: '.$categoires_toString;
  
      $file_contents = file_get_contents(CONF.'routes.yaml');
      $file_contents = str_replace("category_slug: ",$categoires_toString,$file_contents);
      file_put_contents(CONF.'routes.yaml',$file_contents);
    }

    $_SESSION['all_categories'] = [
      'all' => $categories_row,
      'categories' => $categories,
      'subcategories' => $subcategories
    ];
  }

  public function delete(Request $request)
  {
    if($this->role === 'user'){
      $this->echoForbittenMessage();
    }
    $id = (int) $request->attributes->get('id');
    
    $all_categories = $_SESSION['all_categories'];
    
    $category_id_in_session = array_search($id, array_column($all_categories['all'], 'id'));

    $category_info = $all_categories['all'][ $category_id_in_session];
    
    if($category_info['photo']){
      $this->deleteImage($category_info['photo'], 'categories');
    }
    
    if($id){
      $category = new Category();
     
      $article = new Article();
      $articles = $article->getArticles([
        'materials.id', 'materials.photo', 'materials.categories_id'
      ],
      [
        'materials.categories_id' =>  $id 
      ]);
      
      if(count($articles) > 0){
        foreach ($articles as $article) {
          if($article['photo']){
            $this->deleteImage($article['photo'], 'articles');
          }
        }
      }

      $res = $category->deleteCategory(['categories.id'=>$id]);

      if(!$category_info['parent']){
        $categpries = $category->getCategories([], [
          'categories.parent' => $parent
        ]);
        if(count($categpries) > 0){
          foreach ($categpries as $categpry) {

            $articles = $article->getArticles([
              'materials.id', 'materials.photo', 'materials.categories_id'
            ],
            [
              'materials.categories_id' =>  $categpry['id'] 
            ]);

            if(count($articles) > 0){
              foreach ($articles as $article) {
                if($article['photo']){
                  $this->deleteImage($article['photo'], 'articles');
                }
              }
            }
            
            $this->deleteImage($categpry['photo'], 'categories');
            $category->deleteCategory(['categories.id'=> $categpry['id']]);
          }
        }
      }

      $this->refreshCategories();
      echo json_encode([
        'success' => true,
        'res'=>$res
        ]);
      die();
    }
  }

}
