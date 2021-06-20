<?php


namespace models;

use widgets\Navbar;


class AdminModel
{
    public $title; // Тайтл
    public $navbar; //Навигационная
    public $content; // Контентная составляющая
    public $script;

    public function index($params=null)
    {
        if(!isset($_SESSION['admin'])){
        $this->title='Вход в административную часть';
        $this->navbar = Navbar::GetNav('Вход',array('Вернуться на сайт'=>'/'),'admin');
        }
        else{
            $this->title='Административная часть';
            $this->navbar = Navbar::GetNav('Настройки приложения',array('Настройки приложения'=>'/Admin','Управление контентом'=>'/Admin/Content','Пользователи'=>'/Admin/Users'),true);
            $this->content='';
            $this->script='';
        }
    }
}