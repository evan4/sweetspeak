<!DOCTYPE html>
<html lang="ru-Ru">
<head itemscope itemtype="http://schema.org/WPHeader">

    <title><?= $model->title ?></title>
    <meta itemprop="description" name="description" content="<?=$description?>"/>
    
    <meta property="og:description" content="<?=$description?>"/>
    <meta property="og:title" content="<?= $model->title ?>"/>
    <meta property="og:type" content="article" />

    <?php
    /**
     * Вид контроллера IndexController Методы использования (index)
     *
     */

    include('template/header_str.php');
    $post = $model->post[0];
    
    $posts = $relatedPosts;
    $comments_post = $comments;
    ?>
    <meta property="og:url" content="<?=DOMAIN_NAME.$_SERVER['REQUEST_URI']?>" />
    <meta itemprop="image" content="<?=DOMAIN_NAME.$post['photo']['large']?>">
    <meta property='og:image' content='<?=DOMAIN_NAME.$post['photo']['large']?>' />
</head>
<body>
<div id="wrap">
<?php include('template/headerblock_str.php'); ?>

<div class="wrap">
      <div class="col-9" itemprop="itemListElement" itemscope 
        itemtype="https://schema.org/Article">
        <ul class="big-image-breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">
            <li class="big-image-breadcrumbs__item" 
            itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
              <a href="/" itemprop="item">Главная</a>
              <meta itemprop="position" content="1" />
            </li>
            <li class="big-image-breadcrumbs__item"
            itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
              <a href="/<?=$category_slug?>" itemprop="item"><?=$category_title?></a>
              <meta itemprop="position" content="2" />
            </li>
            <li class="big-image-breadcrumbs__item"
            itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
              <a itemprop="item"
              href="/<?=$category_slug?>/<?=$subcategory['slug']?>"><?=$subcategory['name']?></a>
              <meta itemprop="position" content="3" />
            </li>
          </ul>
        <div class="big-image" style="background-image: url(<?=$post['photo']['large']?>);">
         
          <div class="big-image-bottom">
            <ul class="big-image-list big-image-list_article">
              <li class="big-image-list__item big-image-list__item_visibility"><?=$post['see']?><i></i></li>
              <li class="big-image-list__item big-image-list__item_heart"><span class="likes"><?=$post['likes']?></span><i></i></li>
              <li class="big-image-list__item big-image-list__item_dislike"><span class="dislikes"><?=$post['dislikes']?></span><i></i></li>
              <li class="big-image-list__item big-image-list__item_comments"><?=count($comments_post)?><i></i></li>
              <li class="big-image-list__item big-image-list__item_author">
                <a href="<?=$post['author_url']?>" itemprop="author"><?=$post['author']?></a>
              </li>
              <li class="big-image-list__item big-image-list__item_date"> 
                <time itemprop="datePublished" datetime="<?=$post['date']?>"><?=$post['date']?></time>
              </li>
            </ul>
            <ul class="big-image-links">
              <li class="big-image-links__item">
                <span data-id="<?=$post['id']?>" class="big-image-links__link big-image-links__link_favour"></span>
              </li>
              <li class="big-image-links__item">
                <span data-id="<?=$post['id']?>" class="big-image-links__link big-image-links__link_oppose"></span>
              </li>
              <li class="big-image-links__item">
                <span class="big-image-links__link big-image-links__link_share"></span>
              </li>
            </ul>
            <ul class="share">
              <li class="socials__item socials__item_block">
                <span class="socials__link socials__link_telegram"></span>
              </li>
              <li class="socials__item socials__item_block">
                <span class="socials__link socials__link_facebook"></span>
              </li>
              <li class="socials__item socials__item_block">
                <span class="socials__link socials__link_twitter"></span>
              </li>
              <li class="socials__item socials__item_block">
                <span class="socials__link socials__link_vk"></span>
              </li>
              <li class="socials__item socials__item_block">
                <span class="socials__link socials__link_instagram"></span>
              </li>
            </ul>
          </div>
        </div>
        <article class="article">
          <h1 class="caption caption_h2" itemprop="headline"><?=$post['title']?></h1>
          <?php if(isset($post['menu']) && count($post['menu']) > 0) { ?>
          <div class="article-menu">
            <p class="article-menu__title">Содержание статьи</p>
            
              <ul class="article-list">
                <?php
                $i=1;
                foreach ($post['menu'] as $menu) { ?>
                <li>
                  <a href="#section<?=$i?>" class="article-menu__wrap"
                  data-id="<?=$i?>">
                    <span class="article-menu__text">
                      <strong><?=$menu?></strong>
                    </span>
                    <span class="article-menu__line"></span>
                    <span class="article-menu__num"><?=$i?></span>
                  </a>
                </li>
                <?php $i +=1;
                } ?>
                
              </ul>
            
          </div>
          <?php } ?>
          <div class="article__text" itemprop="articleBody"><?=$post['content']?></div>

          <ul class="big-image-links big-image-links_bottom">

            <ul class="big-image-list big-image-list_article">
              <li class="big-image-list__item big-image-list__item_visibility"><?=$post['see']?><i></i></li>
              <li class="big-image-list__item big-image-list__item_heart"><span class="likes"><?=$post['likes']?></span><i></i></li>
              <li class="big-image-list__item big-image-list__item_dislike"><span class="dislikes"><?=$post['dislikes']?></span><i></i></li>
              <li class="big-image-list__item big-image-list__item_comments"><?=count($comments_post)?><i></i></li>
              <li class="big-image-list__item big-image-list__item_author">
                <a href="<?=$post['author_url']?>" itemprop="author"><?=$post['author']?></a>
              </li>
              <li class="big-image-list__item big-image-list__item_date"> 
                <time itemprop="datePublished" datetime="<?=$post['date']?>"><?=$post['date']?></time>
              </li>
            </ul>
            <ul class="big-image-links">

              <li class="big-image-links__item">
                <span data-id="<?=$post['id']?>" class="big-image-links__link big-image-links__link_favour"></span>
              </li>
              <li class="big-image-links__item">
                <span data-id="<?=$post['id']?>" class="big-image-links__link big-image-links__link_oppose"></span>
              </li>
              <li class="big-image-links__item">
                <span class="big-image-links__link big-image-links__link_share"></span>
              </li>
            </ul>
        </article>
        <?php 
        if($comments_post){  ?>
        <h3 class="caption caption_h3">Комментарии к статье:</h3>
        <ul class="comments" id="comments-block"  itemscope itemtype="http://schema.org/Review">
        <?php 
        foreach ($comments_post as $comments)  { 
          if(count($comments) > 1) {
            $first = array_shift($comments);?>
             <li class="comments__item">
              <img src="<?=$first['photo'] != '' ? '/bundles/users/100_'.$first['photo'] : '/bundles/img/no-photo.jpg'?>" 
              class="comments__image" alt="<?=$first['author']?>">
              <div class="comments-text">
                <p class="comments__author">
                  <a href="/author/<?=$first['author_id']?>" itemprop="author"><?=$first['author']?></a> <span>написал:</span></p>
                <p class="article__text" itemprop="name"><?=$first['body']?></p>
                <p class="comments__date">
                  <time itemprop="datePublished" datetime="<?=$first['created_at']?>"><?=$first['created_at']?></time>  
                </p>
              </div>
              <ul class="comments-photos">
              <?php foreach ($first['photos'] as $photo) { ?>
              <li>
                <img src="<?='/bundles/comments/'.$photo['photo']?>" alt="фото комментария">
              </li>
              <?php } ?>
              </ul>
              <div class="comments-answer">
                <button class="button button_red form-comment__button answer-comment">Ответить</button>
                <div class="comments-answer__inner">
                  <form action="" method="post" class="form-comment" enctype="multipart/form-data">
                    <input type="hidden" name="ValidateFormAccess" value="<?=$_SESSION['ValidateFormAccess']?>">
                    <input type="hidden" name="post_id" id="post_id" value="<?=$post['id']?>">
                    <input type="hidden" name="parent" id="parent_id" value="<?=$first['id']?>">
                    <label class="form-comment__label">Ваш комментарий</label>
                    <textarea name="body" rows="3" class="form-comment__textarea"
                      placeholder="Введите сообщение" required></textarea>
                    <div class="form-comment-bottom">
                      <button class="button button_red form-comment__button" type="submit">Отправить</button>
                      <label for="files-<?=$first['id']?>" class="button button_outline form-comment__button">Выбрать файл</label>
                      <input type="file" id="files-<?=$first['id']?>" name="files[]" multiple="multiple" class="file-type"
                      accept="image/jpeg, image/png, image/gif,">
                      <ul class="form-comment-files"></ul>
                      <div class="inputs"></div>
                    </div>
                </form>
                </div>
              </div>
              <ul class="comments comments_sub">
               <?php  
               foreach ($comments as $comment)  { ?>
                <li class="comments__item">
                  <img src="<?=$comment['photo'] != '' ? '/bundles/users/100_'.$comment['photo'] : '/bundles/img/no-photo.jpg'?>" 
                  class="comments__image" alt="<?=$comment['author']?>">
                  <div class="comments-text">
                    <p class="comments__author">
                      <a href="/author/<?=$comment['author_id']?>" itemprop="author"><?=$comment['author']?></a> <span>ответил:</span></p>
                    <p class="article__text" itemprop="name"><?=$comment['body']?></p>
                    <p class="comments__date">
                      <time itemprop="datePublished" datetime="<?=$comment['created_at']?>"><?=$comment['created_at']?></time>  
                    </p>
                  </div>
                  <ul class="comments-photos">
                  <?php foreach ($comment['photos'] as $photo) { ?>
                  <li>
                    <img src="<?='/bundles/comments/'.$photo['photo']?>" alt="фото комментария">
                  </li>
                  <?php } ?>
                  </ul>
                </li>
          <?php } ?>
              </ul>
          </li>
       
          <?php } else { ?>
          <?php  foreach ($comments as $comment)  { ?>
            <li class="comments__item">
              <img src="<?=$comment['photo'] != '' ? '/bundles/users/100_'.$comment['photo'] : '/bundles/img/no-photo.jpg'?>" 
              class="comments__image" alt="<?=$comment['author']?>">
              <div class="comments-text">
                <p class="comments__author">
                  <a href="/authors/<?=$comment['author_id']?>" itemprop="author"><?=$comment['author']?></a> <span>написал:</span></p>
                <p class="article__text" itemprop="name"><?=$comment['body']?></p>
                <p class="comments__date">
                  <time itemprop="datePublished" datetime="<?=$comment['created_at']?>"><?=$comment['created_at']?></time>  
                </p>
              </div>
              <ul class="comments-photos">
              <?php foreach ($comment['photos'] as $photo) { ?>
              <li>
                <img src="<?='/bundles/comments/'.$photo['photo']?>" alt="фото комментария">
              </li>
              <?php } ?>
              </ul>
              <div class="comments-answer">
                <button class="button button_red form-comment__button answer-comment">Ответить</button>
                <div class="comments-answer__inner">
                  <form action="" method="post" class="form-comment" enctype="multipart/form-data">
                    <input type="hidden" name="ValidateFormAccess" value="<?=$_SESSION['ValidateFormAccess']?>">
                    <input type="hidden" name="post_id" value="<?=$post['id']?>">
                    <input type="hidden" name="parent" value="<?=$comment['id']?>">
                    <label for="body" class="form-comment__label">Ваш комментарий</label>
                    <textarea name="body" rows="3" class="form-comment__textarea"
                      placeholder="Введите сообщение" required></textarea>
                    <div class="form-comment-bottom">
                      <button class="button button_red form-comment__button" type="submit">Отправить</button>
                      <label for="files_answer-<?=$comment['id']?>" class="button button_outline form-comment__button">Выбрать файл</label>
                      <input type="file" id="files_answer-<?=$comment['id']?>" name="files[]" multiple="multiple" class="file-type"
                      accept="image/jpeg, image/png, image/gif,">
                      <ul class="form-comment-files"></ul>
                      <div class="inputs"></div>
                    </div>
                </form>
                </div>
              </div>

            </li>
          <?php } ?>

          <?php } ?>
        <?php } ?>
        </ul>
        <?php }?>
        
        
        <h3 class="caption caption_h3 comments__caption">Написать комментарий:</h3>
        
        <form action="" method="post" class="form-comment" enctype="multipart/form-data">
          <input type="hidden" name="ValidateFormAccess"
          value="<?=$_SESSION['ValidateFormAccess']?>">
          <input type="hidden" name="post_id" value="<?=$post['id']?>">
          <label for="body" class="form-comment__label">Ваш комментарий</label>
          <textarea name="body" id="body" rows="3" class="form-comment__textarea"
            placeholder="Введите сообщение" required></textarea>
          <div class="form-comment-bottom">
            <button class="button button_red form-comment__button" type="submit">Отправить</button>
            <label for="files" class="button button_outline form-comment__button">Выбрать файл</label>
            <input type="file" id="files" name="files[]" multiple="multiple" class="file-type"
            accept="image/jpeg, image/png, image/gif,">
            <ul class="form-comment-files"></ul>
            <div class="inputs"></div>
          </div>
        </form>
        <div class="form-comment-auth">
          <p class="form-comment-auth__forbitten"></p>
          <p class="modal__caption">Зарегистрируйтесь</p>
          <p  class="modal__promo">И получите полный доступ ко всем материалам сайта</p>
          <form action="/singup" method="post" class="form" id="singup">
            <input type="hidden" name="ValidateFormAccess" value="<?=$_SESSION['ValidateFormAccess']?>">
            <div class="form-block">
                <label for="namel-form" class="form__label">Ваше Имя:<sup>*</sup></label>
                <input type="text" name="name" class="form__input" id="namel-form" required>
            </div>
            <div class="form-block">
                <label for="email-form" class="form__label">Email:<sup>*</sup></label>
                <input type="email" name="email" class="form__input" id="email-form" required>
            </div>
            <div class="form-block">
              <label for="change_password" class="form__label">Придумайте пароль</label>
              <input type="password" class="form__input" autocomplete="off"
              pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
              title="Не менее восьми символов, содержащих хотя бы одну цифру и символы из верхнего и нижнего регистра"
              name="password" id="change_password" required>
              <button type="button" class="eye-button eye-button_form show-pass"></button>
            </div>
            <div class="form-block">
              <label for="checkbox" class="modal-form__checkboxlabel modal-form__checkboxlabel_singup">
                <input type="checkbox" class="modal-form__check visually-hidden"
                name="check" id="checkbox" required><div class="modal-form__checkbox"></div>
                <span>Регистрируясь, вы принимаете условия обработки персональных данных пользователей</span>
              </label>
            </div>
            <button type="submit" class="button button_red form-comment__button">Зарегестрироваться</button>
        </form>
        </div>
        
      </div>
      <div class="col-3 col-right">
        <?php include('template/aside_str.php'); ?>
      </div>
    </div>
    <?php if($posts) { ?>
    <div class="wrap">
      <h3 class="caption caption_h3">Похожие статьи:</h3>
        <ul class="similars" itemscope itemtype="https://schema.org/ItemList">
        <?php foreach ($posts as $post) { ?>
            <li class="similars__item" itemprop="itemListElement" 
              itemscope itemtype="https://schema.org/Article">
                <div class="similars-image" style="background-image: url(<?=$post['photo']['medium']?>);">
                    <div class="similars-image-top">
                    <span class="similars-image-top__text"><?=$post['category']?></span>
                    </div>
                    <ul class="similars-list">
                    <li class="similars-list__item similars-list__item_visibility"><?=$post['see']?><i></i></li>
                    <li class="similars-list__item similars-list__item_heart"><?=$post['likes']?><i></i></li>
                    <li class="similars-list__item similars-list__item_dislike"><?=$post['dislikes']?><i></i></li>
                    <li class="similars-list__item similars-list__item_comments"><?=$post['comments_count']?><i></i></li>
                    </ul>
                </div>
                <a href="<?=$post["url"]?>" class="similars__caption" itemprop="headline"><?=$post['title']?></a>
                <p class="similars__text" itemprop="articleBody"><?=$post['snippet']?></p>
                <ul class="similars-details">
                    <li class="similars-details__item">
                      <a href="<?=$post['author_url']?>" itemprop="author"><?=$post['author']?></a>
                    </li>
                    <li class="similars-details__item similars-details__item_history"><i></i>
                      <time itemprop="datePublished" datetime="<?=$post['date']?>"><?=$post['date']?></time>  
                    </li>
                </ul>
            </li>
        <?php } ?>
        </ul>
    </div>
    <?php } ?>
</main>
</div>
<?php  include('template/footer_str.php'); ?>

<script src="/bundles/js/postPage.js"></script>

</body>
</html>
