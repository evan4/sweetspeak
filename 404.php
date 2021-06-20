<?php 
header("HTTP/1.0 404 Not Found");
?>
<!DOCTYPE html>
<html lang="ru-Ru">
<head>
    <title>404</title>
<?php
/**
 * Вид контроллера IndexController Методы использования (index)
 *
 */

include('template/header_str.php');

?>
</head>
<body>
<div id="wrap">
<?php

include('template/headerblock_str.php');

?>
 <div class="wrap">
    <div class="notfound">
      <div class="notfound__caption"></div>
      <p class="notfound__text"> К сожалению, нам не удалось найти такую страницу :(  </p>
      <p class="notfound__text_small">Возможно, она была перенесена или переименована.</p class="notfound__text">
      <a href="/" class="button button_red notfound__button">Вернуться на главную</a>
    </div>
  </div>
</main>
</div>
<?php
include('template/footer_str.php');
?>
</body>
</html>
