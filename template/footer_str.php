  <footer class="footer clearfix" itemscope itemtype="http://schema.org/WPFooter">
    <div class="wrap">
      <div class="footer__wrap">
        <div class="footer-socials">
          <?php if($_SERVER['REQUEST_URI'] === '/') { ?>
            <span class="footer-socials__logo"></span>
          <?php }else { ?>
            <a href="/" class="footer-socials__logo"></a>
          <?php } ?>
          <ul class="footer-socials-list">
            <li>
              <a href="<?=TELEGRAM;?>" rel="nofollow"
              class="footer-socials-list__link footer-socials-list__link_telegram"></a>
            </li>
            <li>
              <a href="<?=INSTAGRAM;?>" rel="nofollow"
              class="footer-socials-list__link footer-socials-list__link_instagram"></a>
            </li>
            <li>
              <a href="<?=VK;?>" rel="nofollow"
              class="footer-socials-list__link footer-socials-list__link_vk"></a>
            </li>
            <li>
              <a href="<?=TWITTER;?>" rel="nofollow"
              class="footer-socials-list__link footer-socials-list__link_twitter"></a>
            </li>
            <li>
                <a href="<?=FACEBOOK;?>" rel="nofollow"
                class="footer-socials-list__link footer-socials-list__link_facebook"></a>
            </li>
          </ul>
          <p class="footer-socials__text">Мы позиционируем себя как новую социальную сеть, где любой желающий может
            анонимно говорить и задать вопросы, писать свое мнение</p>
          <p class="footer-socials__caution">Материалы сайта предназначены для возрасной категории 18+</p>
        </div>
        <ul class="footer-menu footer-menu_big">
        <?php foreach ($categories['categories'] as $key => $category) { ?>
          <li>
            <?php if($_SERVER['REQUEST_URI'] === $category) { ?>
              <span><?=$key?></span>
            <?php }else { ?>
              <a href="<?=$category?>"><?=$key?></a>
            <?php } ?>
            
          </li>
        <?php } ?>
          <li>
          <?php if($_SERVER['REQUEST_URI'] === '/authors') { ?>
            <span>Все авторы</span>
          <?php }else { ?>
            <a href="/authors">Все авторы</a>
          <?php } ?>
          </li>
        </ul>
        <div class="footer-subscribe">
          <p class="footer-subscribe__caption">Подпишитесь на уведомления</p>
          <p class="footer-subscribe__info">Статьи, новости ресурса и уведомления</p>
          <form action="" method="post" class="footer-form">
            <input type="hidden" name="ValidateFormAccess" value="<?=$_SESSION['ValidateFormAccess']?>">
            <input type="email" name="email" id="email" required
            class="footer-form__input" placeholder="Введите E-mail">
            <button type="submit" class="footer-form__submit button button_red">Подписаться</button>
          </form>
        </div>
        <div class="footer-profile">
          <?php if($user_login) {  ?>

            <?php if(strpos($_SERVER['REQUEST_URI'], 'Dashboard') === false) { ?>
            <a href="/Dashboard/index"
              class="header__profile header__profile_outline button button_outline">
                <span class="header__span"><?=substr($_SESSION['User'], 0, 14);?></span>
            </a>
            <?php } else { ?>
            <span class="header__profile header__profile_outline button button_outline">
              <span class="header__span"><?=substr($_SESSION['User'], 0, 14);?></span>
            </span>
            <?php } ?>
          <?php }else { ?>
          <a href="#registry-form"
          class="header__profile header__profile_outline button button_outline" >
            <span class="header__span">Профиль</span>
          </a>
          <?php } ?>
          <ul class="footer-menu footer-menu_right">
            <li>
              <?php if($_SERVER['REQUEST_URI'] === '/privacy') { ?>
                <span>Политика конфиденциальности</span>
              <?php }else { ?>
                <a href="/privacy">Политика конфиденциальности</a>
              <?php } ?>
            </li>
            <li>
              <?php if($_SERVER['REQUEST_URI'] === '/about') { ?>
                <span>О проекте</span>
              <?php }else { ?>
                <a href="/about">О проекте</a>
              <?php } ?>
            </li>
            <li>
              <?php if($_SERVER['REQUEST_URI'] === '/for_experts') { ?>
                <span>Для блогеров</span>
              <?php }else { ?>
                <a href="/for_experts">Для блогеров</a>
              <?php } ?>
            </li>
            <li>
            <?php if($_SERVER['REQUEST_URI'] === '/rules') { ?>
              <span>Правила</span>
            <?php }else { ?>
              <a href="/rules">Правила</a>
            <?php } ?>
            </li>
          </ul>
          <meta itemprop="copyrightYear" content="<?=date('Y')?>">
          <meta itemprop="copyrightHolder" content="Maximus Media">
          <p class="footer__copyright"><?=date('Y')?> &copy; Все права защищены</p>
          <a class="footer__logo-maximus" rel="nofollow"  href="https://maximusmedia.pro/" 
          target="_blank">Разработка и продвижение сайта:</a>
        </div>
      </div>
    </div>
  </footer>
  
  <div id="registry-form" class="modal mfp-hide">
    <div id="registration">
      <p class="modal__caption">Зарегистрируйтесь</p>
      <p  class="modal__promo">И получите полный доступ ко всем материалам сайта</p>
      <form action="/singup" method="POST" class="modal-form" id="form-registration">
        <div class="modal-form-block modal-form-block_first">
          <label for="registration_name" class="modal-form__label">Ваше имя</label>
          <input type="text" class="modal-form__input"
          name="name" id="registration_name" required>
        </div>
        <div class="modal-form-block">
          <label for="registration_email" class="modal-form__label">Email</label>
          <input type="email" class="modal-form__input"
          name="email" id="registration_email" id="registration_email" required>
        </div>
        <div class="modal-form-block">
          <label for="registration_password" class="modal-form__label">Придумайте пароль</label>
          <input type="password" class="modal-form__input" autocomplete="off"
          pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
          title="Не менее восьми символов, содержащих хотя бы одну цифру и символы из верхнего и нижнего регистра"
          name="password" id="registration_password" required>
          <button type="button" class="eye-button show-pass"></button>
        </div>
        <div class="modal-form-block modal-form-block_checkbox">
          <label for="check" class="modal-form__checkboxlabel">
          <input type="checkbox" class="modal-form__check visually-hidden"
          name="check" id="check" required><div class="modal-form__checkbox"></div>
          <span>Регистрируясь, вы принимаете условия обработки персональных данных пользователей</span>
        </label>
        </div>
        <button type="submit" data-type="pos_aware"
        class="button button_outline modal-form__submit">Быстрая регистрация</button>
      </form>
      <ul class="error"></ul>
      <a href="#authorization" class="modal__toggle">Уже зарегестрированы?</a>
    </div>
    <div id="authorization">
      <p class="modal__caption">Авторизация</p>
      <form action="/auth" method="POST" class="modal-form" id="form-auth">
        <div class="modal-form-block modal-form-block_first">
          <label for="authorization_email" class="modal-form__label">Email</label>
          <input type="email" class="modal-form__input"
          name="email" id="authorization_email"
          placeholder="Ваш email"
          required>
        </div>
        <div class="modal-form-block">
          <label for="authorization_password" class="modal-form__label">Пароль</label>
          <input type="password" class="modal-form__input" autocomplete="off"
          name="password" id="authorization_password" 
          placeholder="Ваш пароль"
          required>
          <i class="modal-form__show show-pass"></i>
        </div>
        <button type="submit" data-type="pos_aware"
        class="button button_outline modal-form__submit">Войти</button>
      </form>
      <ul class="error"></ul>
      <ul class="modal-list">
        <li>
          <a href="#registration" class="modal__toggle">Регистрация</a>
        </li>
        <li>
          <a href="#forgot" class="modal__toggle">Забыли пароль?</a>
        </li>
      </ul>
    </div>
    <div id="forgot">
      <p class="modal__caption">Введите email</p>
      <form action="/recoveryPassword" method="POST" class="modal-form" id="form-recovery">
        <div class="modal-form-block">
          <label for="recovery_email" class="modal-form__label">Email</label>
          <input type="email" class="modal-form__input"
          name="email" id="recovery_email" id="recovery_email"
          placeholder="Ваш email"
          required>
        </div>
        <button type="submit" data-type="pos_aware"
        class="button button_outline modal-form__submit">Отправить</button>
      </form>
      <ul class="error"></ul>
      <ul class="modal-list">
        <li>
          <a href="#registration" class="modal__toggle">Регистрация</a>
        </li>
        <li>
          <a href="#authorization" class="modal__toggle">Уже зарегестрированы?</a>
        </li>
      </ul>
    </div>
  </div>
<!--Main js file Style-->
<script src="/bundles/js/libs/jquery-3.5.1.min.js"></script>
<script src="/bundles/js/libs/libs.js"></script>
<?php
$current_uri = $_SERVER['REQUEST_URI'];

if(!$user_login && (
  $current_uri === '/' || $current_uri === '/?theme=day' || $current_uri === '/?theme=night' 
)){ ?>
<script src="/bundles/js/libs/slick.min.js"></script>
<script src="/bundles/js/guest.js"></script>
<?php } else { ?>
<script src="/bundles/js/main.js?ver=<?=time()?>"></script>
<?php }  ?>