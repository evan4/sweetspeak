<?php

namespace core;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

class Controller 
{
  public $session;
  public $twig;

  public function __construct()
  {
      $sessionStorage = new NativeSessionStorage(array(
          "name" => "sweetspeak",
          "cookie_lifetime" => 86400
      ), new NativeFileSessionHandler());
      $this->session = new Session($sessionStorage);
      $this->session->start();
      var_dump($sessionStorage);die();

      $loader = new \Twig\Loader\FilesystemLoader(ROOT.'/template');
      $this->twig = new \Twig\Environment($loader, [
          'cache' => false,
          //'cache' => ROOT.'/cache',
          'auto_reload' => true,
          'debug' => true
      ]);
      
  }
}
