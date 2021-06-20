<?php
  $sort_data = [
    1 => 'сегодня',
    5 => '5 дней', 
    30 => 'месяц'
  ];
  $uri = $_SERVER['REQUEST_URI'];
  
  $search_page = false;
  if(mb_stripos($uri,'/search' !== false) || mb_stripos($uri,'/author/articles' !== false )){
    $search_page = true;
  }
  
  if(mb_stripos($uri,'/search' !== false) ){
    $uri.= '&search='.$_GET['search'];
  }
?>
<form action="<?=$uri?>" method="get" id="filter">

  <div class="form-block form-block_inline <?=$search_page === false ? 'form-block_three' : ''?>">
  <?php if($search_page !== false) { ?>
  <input type="hidden" name="search" value="<?=filter_var( trim($_GET['search'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS)?>">
    <div class="form-block__filter">
      <select name="categories" id="categories" class="subcategory">
        <option value="">По категории</option>
        <?php foreach ( $_SESSION['all_categories']['categories'] as $key => $category) { ?>
          <option value="<?=$category?>" <?= (isset($_GET['categories']) 
          && !empty($_GET['categories']) && 
          filter_var( $_GET['categories'], FILTER_SANITIZE_SPECIAL_CHARS) === $category)? 'selected' : '';?>><?=$key?></option>
        <?php } ?>
      </select>
    </div>
    <?php } ?>
    <div class="form-block__filter">
      <select name="datesort" id="datesort" class="subcategory">
        <option value="">По дате</option>
        <?php foreach ($sort_data as $key => $value) { ?>
          <option value="<?=$key?>" <?= (isset($_GET['datesort']) && !empty($_GET['datesort']) 
          && intval($_GET['datesort']) === $key)? 'selected' : '';?>><?=$value?></option>
        <?php } ?>
      </select>
    </div>
    
    <div class="form-block__filter">
      <?php 
      if(isset($_GET['popularity']) && !empty($_GET['popularity'])) { 
          $popularity =  filter_var( $_GET['popularity'], FILTER_SANITIZE_SPECIAL_CHARS);
          if($popularity === 'ascending' || $popularity === 'descending' ){ ?>
        <span class="form__input form__input_sort" aria-sort="<?= $popularity?>">По популярности</span>
        <input type="text" name="popularity" class="hidden" value="<?=$popularity?>">
        <?php } ?>
      <?php }else { ?>
        <span class="form__input form__input_sort" aria-sort="ascending">По популярности</span>
        <input type="text" name="popularity" class="hidden" value="ascending">
        <?php } ?>
    </div>
    <div class="form-block__filter">
    <?php if(isset($_GET['comments']) && !empty($_GET['comments'])) { 
          $comments_sort =  filter_var( $_GET['comments'], FILTER_SANITIZE_SPECIAL_CHARS);
          if($comments_sort === 'ascending' || $comments_sort === 'descending' ){ ?>
        <span class="form__input form__input_sort" aria-sort="<?= $comments_sort?>">По комментариям</span>
        <input type="text" name="comments" class="hidden" value="<?=$comments_sort?>">
        <?php } ?>
      <?php } else { ?>
        <span class="form__input form__input_sort" aria-sort="ascending">По комментариям</span>
        <input type="text" name="comments" class="hidden">
        <?php } ?>
    </div>
  </div>
  <button class="button button_red form-comment__button" type="submit">Сортировать</button>
</form>