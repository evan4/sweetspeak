<?php 

$categories = $_SESSION['all_categories'];
$current_uri = $_SERVER['REQUEST_URI'];

?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PQ8F5N2"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<header class="header">
  <div class="wrap">
    <div class="header_wrap">
      <button class="burger">
        <span></span>
      </button>
      <div class="header-invert">
        <a href="#" data-theme="night" class="theme-change header-invert__day"></a>
        <a href="#" data-theme="day" class="theme-change header-invert__night"></a>
      </div>
      <ul class="header-menu" itemscope itemtype="http://schema.org/SiteNavigationElement">
        <?php foreach ($categories['categories'] as $key => $category) { ?>
          <li class="header-menu__item">
            <a href="/<?=$category?>" class="header-menu__link"><?=$key?></a>
              <ul class="header-menu-sub">
                <?php foreach ($categories['subcategories'][$category] as $key => $value) { ?>
                  <li>
                    <a itemprop="url" href="/<?=$category?>/<?=$value?>"><?=$key?></a>
                  </li>
                <?php  } ?>
              </ul>
          </li>
        <?php } ?>
      </ul>
      <?php if($user_login) {  ?>
        <?php 
         if(strpos($_SERVER['REQUEST_URI'], 'dashboard') === false) { ?>
          <a href="/dashboard"  class="header__profile button button_red">
          <span class="header__span"><?=substr($_SESSION['User'], 0, 14);?></span>
        </a>
        <?php }else { ?>
        <span class="header__profile button button_red">
          <span class="header__span"><?=substr($_SESSION['User'], 0, 14);?></span>
        </span>
        <?php } ?>
      <?php }else { ?>
      <a href="#registry-form" class="header__profile button button_red">
        <span class="header__span">Профиль</span>
      </a>
      <?php } ?>
    </div>
  </div>
  <ul class="header-menu-modile">
    <?php foreach ($categories['categories'] as $key => $category) { ?>
      <li class="header-menu__item">
        <a href="/<?=$category?>" class="header-menu__link"><?=$key?></a>
          <ul class="header-menu-sub">
            <?php foreach ($categories['subcategories'][$category] as $key => $value) { ?>
              <li>
                <a href="/<?=$category?>/<?=$value?>"><?=$key?></a>
              </li>
            <?php  } ?>
          </ul>
      </li>
    <?php } ?>
  </ul>
</header>
<main class="main">
  <div class="wrap">
    <section class="top clearfix">
      <?php if($_SERVER['REQUEST_URI'] === '/') { ?>
      <span class="top__logo"></span>
     <?php }else { ?>
      <a href="/" class="top__logo"></a>
      <?php } ?>
      <form action="/search" method="get" class="top__form">
        <input type="search" class="top__search" name="search" 
        placeholder="Например, секс втроем" 
        value="<?=filter_var( trim($_GET['search'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS)?>">
      </form>
  
      <?php
      if($user_login && strpos($_SERVER['REQUEST_URI'], '/article') == false ) { ?>
        <a href="/article" class="button button_red top__add">
          <i class="top__plus"></i>
          <span>Добавить статью</span>
        </a>
      <?php } ?>
      <ul class="top-socials">
        <li>
          <a href="<?=TELEGRAM;?>" rel="nofollow"
          class="top-socials__link top-socials__link_telegram" target="_blank"></a>
        </li>
        <li>
          <a href="<?=INSTAGRAM;?>" rel="nofollow"
          class="top-socials__link top-socials__link_instagram" target="_blank"></a>
        </li>
      </ul>
    </section>
  </div>