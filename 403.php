<?php 
header("HTTP/1.0 403 Forbidden");
?>
<!DOCTYPE html>
<html lang="ru-Ru">
<head>
    <title><?= $model->title ?></title>
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
    <div>
      <p class="notfound__text"> У вас нет доступа к данной странице</p>
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
