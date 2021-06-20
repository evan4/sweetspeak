<!DOCTYPE html>
<html lang="ru-Ru">
<head itemscope itemtype="http://schema.org/WPHeader">
    <title itemprop="headline"><?= $model->title ?></title>
    <meta itemprop="description" name="description" content="<?=$description?>">
    <meta property="og:description" content="<?=$description?>"/>
    <meta property="og:title" content="<?= $model->title ?>"/>
    <meta property="og:type" content="website" />
    <meta property='og:image' content='//bundles/images/dest/pair.png' />
    <?php
    /**
     * Вид контроллера IndexController Методы использования (index)
     *
     */

    include('template/header_str.php');
    
    $posts = $model->posts;
    ?>
</head>
<body>
<div id="wrap">
<?php include('template/headerblock_str.php'); ?>
<div class="wrap wrap_main">
  <div class="col-9">

    <div class="top-main">
      <h1 class="caption caption_h1">Результаты поиска</h1>
      <div class="top-main-buttons">
        <span class="button button_outline top-main-buttons__more button_text">Всего: <span class="categories__amount"><?=$posts['total']?>+</span>
        </span>
      </div>
    </div>
    <?php include('template/filter.php'); ?>
    <?php if($posts['posts']){ ?>
    <ul class="similars similars_margin">
      <?php foreach ($posts['posts'] as $post) { ?>
      <li class="similars__item">
        <div class="similars-image" style="background-image: url(<?=$post['photo']['medium'];?>);">
          <div class="similars-image-top">
            <span class="similars-image-top__text"><?=$post["category"]?></span>
          </div>
          <ul class="similars-list">
            <li class="similars-list__item similars-list__item_visibility"><?=$post["see"]?><i></i></li>
            <li class="similars-list__item similars-list__item_heart"><?=$post["likes"]?><i></i></li>
            <li class="similars-list__item similars-list__item_dislike"><?=$post["dislikes"]?><i></i></li>
            <li class="similars-list__item similars-list__item_comments"><?=$post["comments_count"]?><i></i></li>
          </ul>
        </div>
        <a href="<?=$post["url"]?>" class="similars__caption"><?=$post["title"]?></a>
        <p class="similars__text"><?=$post["snippet"]?></p>
        <ul class="similars-details">
          <li class="similars-details__item">
            <a href="<?=$post["author_url"]?>"><?=$post["author"]?></a>
          </li>
          <li class="similars-details__item similars-details__item_history"><i></i> <?=$post["date"]?></li>
        </ul>
      </li>
      <?php } ?>
      </ul>
      <?=$posts['pagination'];?>
      <?php }else { ?>
      <h4 class="caption caption_h4">По данному запросу ничего не найдено</h4>
      <?php } ?>
  </div>
 
  
  <div class="col-3 col-right">
    <?php include('template/aside_str.php'); ?>
  </div>
</div>
</main>
</div>
<?php include('template/footer_str.php'); ?>
</body>
</html>




