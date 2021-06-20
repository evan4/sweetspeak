<?php

namespace models;

use core\CRUD;

class CategoryTable
{
  public static function categoriesFormat()
  {
    $categories_instant= new CRUD('categories');
    $categories_row = $categories_instant->GetInfo()->Resulting;
    $subcategories = [];
    $parents = [];

    foreach($categories_row as $category){
      if($category['parent'] == 0){
        $categories[$category['name']] = $category['slug'];
        $parents[$category['id']] = $category['slug'];
      }else{
        $subcategories[$parents[$category['parent']]][$category['name']] = $category['slug'];
      }
    }
    
    return [
      'all' => $categories_row,
      'categories' => $categories,
      'subcategories' => $subcategories
    ];

  }

}
