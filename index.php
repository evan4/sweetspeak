<?php //error_reporting( E_ERROR && E_WARNING ); ?>
<?php 
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
?>
<?php
session_start([
    'cookie_lifetime' => 86400,
]);
if( $_SERVER['REQUEST_URI'] == "/index.php" || $_SERVER['REQUEST_URI'] == "/index.html" ) {
    header( "Location: /", TRUE, 301 );
    exit();
}
$app = require_once __DIR__.'/bootstrap/init.php';
$app->run();

if(!isset($_SESSION['ValidateFormAccess']) || empty($_SESSION['ValidateFormAccess'])){ // Если у нас не получен одноразывый ключ для работы с формой, то присваеваем его
    $_SESSION['ValidateFormAccess'] = uniqid('', true); // Присвоение Одноразового пароля валидации на право отправить запрос
}
