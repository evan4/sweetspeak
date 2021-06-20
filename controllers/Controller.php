<?php

namespace controllers;

class Controller
{
  public $twig;

  public function __construct()
  {
    
    $loader = new \Twig\Loader\FilesystemLoader(ROOT.'/template');
    $this->twig = new \Twig\Environment($loader, [
        'cache' => false,
        //'cache' => ROOT.'/cache',
        'auto_reload' => true,
        'debug' => true
    ]);
    $this->twig->addExtension(new \Twig\Extension\DebugExtension());
    $this->twig->addGlobal('session', $_SESSION);
    $this->twig->addGlobal('_get', $_GET);
    $this->twig->addGlobal('current_uri', $_SERVER['REQUEST_URI']);
    $this->twig->addGlobal('url_parts', parse_url($_SERVER['REQUEST_URI']));
  }
}
