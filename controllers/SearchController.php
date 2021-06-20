<?php

namespace controllers;

use models\PostModel;

class SearchController
{
  public function index()
  {
      $limit = 12;
      $request = filter_var( trim($_GET['search']), FILTER_SANITIZE_SPECIAL_CHARS);
      
      $page = (empty($_GET['page']) ? 1 : intval($_GET['page']));
      $offset = $page === 1 ? 0 : ($page -1) * $limit;

      $sort = [];

      if(isset($_GET['categories']) && !empty($_GET['categories'])){
        $sort['categories']= filter_var( trim($_GET['categories']), FILTER_SANITIZE_SPECIAL_CHARS);
      }

      if(isset($_GET['datesort']) && !empty($_GET['datesort'])){
          $sort['datesort']= filter_var( trim($_GET['datesort']), FILTER_SANITIZE_SPECIAL_CHARS);
      }

      if(isset($_GET['popularity']) && !empty($_GET['popularity'])){
          $sort['popularity']= filter_var( trim($_GET['popularity']), FILTER_SANITIZE_SPECIAL_CHARS);
      }

      if(isset($_GET['comments']) && !empty($_GET['comments'])){
          $sort['comments']= filter_var( trim($_GET['comments']), FILTER_SANITIZE_SPECIAL_CHARS);
      }
      
      $model = new PostModel();
      $model->getPosts($request, [
        'sort' => $sort,
        'limit' => $limit,
        'offset' => $offset,
        'page' => $page
      ]);

      $model->url = '/search?search='.$request;
      require $model->template;
  }
}
