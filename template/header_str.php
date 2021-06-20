<meta charset="utf-8">
<meta content="width=device-width,initial-scale=1,minimum-scale=0.86" name="viewport">
<meta name="yandex-verification" content="5d4f4efe69a930ad" />
<meta property="og:locale" content="ru_RU" />
<meta property="og:site_name" content="sweetspeak.ru" />
<meta http-equiv="Cache-control" content="max-age=31536000;public">
<meta name="MobileOptimized" content="320">
<meta name="yandex-verification" content="276c7892663f0745" />
<meta name="yandex-verification" content="64ed3af51afe8b3b" />
<!--Start Style -->
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Montserrat&family=Raleway:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

<?php
$user_login = isset($_SESSION['User']) && !empty($_SESSION['User']) ?  true : false;

$current_uri = $_SERVER['REQUEST_URI'];

if(!$user_login && $current_uri == '/'){
  $theme = (isset($_SESSION['theme']) &&  $_SESSION['theme'] === 'night' ) ? 'guest-night' : 'guest-day';
}else { 
$theme = (isset($_SESSION['theme']) &&  $_SESSION['theme'] === 'night' ) ? 'night' :'day';
} 

?>
<link rel="stylesheet" type="text/css" href="/bundles/css/<?=$theme?>.css">
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PQ8F5N2');</script>
<!-- End Google Tag Manager -->