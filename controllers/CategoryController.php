<?php

namespace controllers;
use models\CategoryModel;
use models\PostModel;
use models\CategoryTable;

class CategoryController
{

    public function getCategories()
    {    
        $nav_pages = [];

        if(isset($_SESSION['all_categories']) && !empty($_SESSION['all_categories'])){
            return $_SESSION['all_categories'];
        }else{
            $_SESSION['all_categories'] = CategoryTable::categoriesFormat();

            return CategoryTable::categoriesFormat();;
        }
        
    }
    public function category($category_slug)
    {
        $nav_pages = $this->getCategories();
        
        $categories = $nav_pages['categories'];
        $subcategories = $nav_pages['subcategories'];
       
        $sort = [];

        if(isset($_GET['datesort']) && !empty($_GET['datesort'])){
            $sort['datesort']= filter_var( trim($_GET['datesort']), FILTER_SANITIZE_SPECIAL_CHARS);
        }

        if(isset($_GET['popularity']) && !empty($_GET['popularity'])){
            $sort['popularity']= filter_var( trim($_GET['popularity']), FILTER_SANITIZE_SPECIAL_CHARS);
        }

        if(isset($_GET['comments']) && !empty($_GET['comments'])){
            $sort['comments']= filter_var( trim($_GET['comments']), FILTER_SANITIZE_SPECIAL_CHARS);
        }
        
        $parent = $category_slug;
        
        $parent_title = array_search($parent, $categories);
        
        $parent_category_id = array_search($parent, array_column($nav_pages['all'], 'slug', 'id'));
        $photo = array_search($parent, array_column($nav_pages['all'], 'photo', 'id'));
        $title = array_search($parent, array_column($nav_pages['all'], 'slug', 'title')) ;
        if(!$title){
            $title = $parent_title;
        }
        $description = array_search($parent, array_column($nav_pages['all'], 'slug', 'description'));
        if(!$description){
            $description = $parent_title;
        }
        if(!$parent_title){
            require '404.php';die();
        }
        
        $category = [
            'parent' => null,
            'category_current' => [
                'id' => $parent_category_id,
                'title' => $parent_title,
                'code' => $parent
            ],
            'subcategories' => $subcategories[$parent],
        ];
        
        $category['sort'] = $sort;
        
        $model = new CategoryModel();
        
        $model->geteData($category);
        $model->category['info'] = $category;
        
        if($photo){
            $model->category['photo'] = '/bundles/categories/'.$all_categories[$parent_category_id]['photo'];
        }
        
        require $model->template;
    }

    public function subcategory($category_slug, $subcategory_slug)
    {
        $nav_pages = $this->getCategories();
        $categories = $nav_pages['categories'];
        $subcategories = $nav_pages['subcategories'];
        
        $all_categories = $nav_pages['all'];
        $categories = $nav_pages['categories'];
        $subcategories = $nav_pages['subcategories'];
        
        $sort = [];

        if(isset($_GET['datesort']) && !empty($_GET['datesort'])){
            $sort['datesort']= filter_var( trim($_GET['datesort']), FILTER_SANITIZE_SPECIAL_CHARS);
        }

        if(isset($_GET['popularity']) && !empty($_GET['popularity'])){
            $sort['popularity']= filter_var( trim($_GET['popularity']), FILTER_SANITIZE_SPECIAL_CHARS);
        }

        if(isset($_GET['comments']) && !empty($_GET['comments'])){
            $sort['comments']= filter_var( trim($_GET['comments']), FILTER_SANITIZE_SPECIAL_CHARS);
        }
        
        $parent = $category_slug;
        
        $parent_title = array_search($parent, $categories);
        
        $parent_category_id = array_search($parent, array_column($all_categories, 'slug', 'id'));
        
        $subparent_title ='';
            
        $subparent_title  = array_search($subcategory_slug, $subcategories[$parent]);

        $subparent_category_id = array_search($subcategory_slug, array_column($all_categories, 'slug'));
        
        $subparent_id = $all_categories[$subparent_category_id]['id'];
     
        if( !$subparent_title){
            require '404.php';die();
        }
        $category = [
            'parent' => [
                'id' => $parent_category_id,
                'title' => $parent_title,
                'code' => $parent,
            ],
            'subcategory_current' => [
                'id' => $subparent_id,
                'title' => $subparent_title,
                'code' => $subcategory_slug
            ],
            'subcategories' => $subcategories[$parent],
        ];
        
        $model = new CategoryModel();
        
        $model->geteData($category);
        $photo = $all_categories[$subparent_category_id]['photo'];
        $model->category['info'] = $category;
        
        if($photo){
            $model->category['photo'] = '/bundles/categories/'.$all_categories[$subparent_category_id]['photo'];
        }

        $title =  $all_categories[$subparent_category_id]['title'];
        if(!$title){
            $title = $subparent_title;
        }
        $description =  $all_categories[$subparent_category_id]['description'];
        if(!$description){
            $description = $category['parent']['title'].' '.$category['subcategory_current']['title'];
        }


        require $model->template;
    }

    public function article($article_slug)
    {
    
        $model = new PostModel();
        
        $model->getPost($article_slug);
        
        $model->increaseNumbersVisitors($model->post[0]["id"]);
        
        if($model->post[0]["slug"] != $article_slug){
            require '404.php';die();
        }
        
        $nav_pages = $_SESSION['all_categories'];
        
        $category_id = array_search($model->post[0]['categories_id'], array_column($nav_pages['all'], 'id'));
        
        $subcategory = $nav_pages['all'][$category_id];
        
        $category = array_slice($nav_pages['categories'], $subcategory['parent']-1, 1); 
        
        $category_title =  array_keys($category)[0];
        $category_slug = array_values($category)[0];

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $res = $dom->loadHTML( '<meta charset="utf-8">' .$model->post[0]['content']);
        libxml_use_internal_errors(false);
        $tags = $dom->getElementsByTagName('h2');
        
        foreach ($tags  as $tag) {
            $string=trim($tag->nodeValue);
            
            if(strlen($string) > 3 && $string != '&nbsp;') {
                $model->post[0]['menu'][] = $string;
            }
            
        }

        $id = $model->post[0]["id"];
        $comments = $model->getComments($id);
        
        $relatedPosts =$model->getRealatedPosts( $model->post[0]['categories_id'], $id);

        $model->title = $model->post[0]['titleseo'] ? $model->post[0]['titleseo'] : $model->post[0]['title'];
        
        $description = $model->post[0]['description'] ? $model->post[0]['description'] : $model->title;

        require $model->template;
       
    }
}
