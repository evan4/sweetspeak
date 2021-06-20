<aside class="aside">
  <p class="aside__caption">Популярные категории:</p>
  <ul class="categories" >
  <?php 
  foreach ($_SESSION['popularCategories'] as $category) { ?>
    <li class="categories__item">
    <?php if($category["url"] == $_SERVER['REQUEST_URI']){ ?>
      <span class="categories__link">
        <span class="categories__text"><?=$category['title']?></span>
        <span class="categories__amount">+<?=$category['total']?></span>
      </span>
    <?php  } else { ?>
      <a href="<?=$category["url"]?>" class="categories__link">
        <span class="categories__text"><?=$category['title']?></span>
        <span class="categories__amount">+<?=$category['total']?></span>
      </a>
    <?php } ?>
    </li>
  <?php } ?>
  </ul>
  <p class="aside__caption">Следите за нами в соцсетях:</p>
    <ul class="socials" >
      <li class="socials__item">
        <a href="<?=TELEGRAM;?>" class="socials__link socials__link_telegram"></a>
        <!-- <span id="numTelegramFollowers"></span> -->
      </li>
      <li class="socials__item">
        <a href="<?=FACEBOOK;?>" class="socials__link socials__link_facebook"></a>
        <!-- <span id="numFacebookFollowers"></span> -->
      </li>
      <li class="socials__item">
        <a href="<?=TWITTER;?>" class="socials__link socials__link_twitter"></a>
        <!-- <span id="numTwitterFollowers"></span> -->
      </li>
      <li class="socials__item">
        <a href="<?=VK;?>" class="socials__link socials__link_vk"></a>
        <!-- <span id="numVkFollowers"></span> -->
      </li>
      <li class="socials__item">
        <a href="<?=INSTAGRAM;?>" class="socials__link socials__link_instagram"></a>
        <!-- <span id="numInstagramFollowers"></span> -->
      </li>
    </ul>
  <p class="aside__caption">Самые обсуждаемые посты и статьи:</p>
  <ul class="posts-list">
  <?php foreach ($_SESSION['popularPosts'] as $popularPost) { ?>
  <li class="posts-list__item">
    <img src="<?=$popularPost['photo']['medium'];?>" alt="post" class="posts-list__image">
    <div class="posts-list__content">
      <?php if($popularPost['url'] == $_SERVER['REQUEST_URI']){ ?>
        <span class="posts-list__text"><?=mb_substr($popularPost['title'], 0, 60);?></span>
      <?php  } else { ?>
        <a href="<?=$popularPost['url'];?>" class="posts-list__text"><?=mb_substr($popularPost['title'], 0, 60);?></a>
      <?php } ?>

      <span class="posts-list__author">
        <a href="<?=$popularPost['author_url'];?>"><?=$popularPost['author'];?></a>
      </span class="posts-list__text">
    </div>
  </li>
  <?php } ?>
  </ul>
  <p class="aside__caption">Самое интересное в нашем Instagram:</p>
  <div class="aside-gallery aside-gallery-instagram"> </div>
</aside>
<!-- <div class="aside-proposal">
  <span>Пример баннера</span>
</div> -->
