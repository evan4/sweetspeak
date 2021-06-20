<?php
/**
 * модель создания индексной страницы
 *
 *
 */

namespace models;

use core\Model;


class PoliticModel Extends Model
{
    /**
     *  parent:: pages - содержание списка страниц проекта
     *
     * content - содержание контентной части индексной страницы
     * title -  значения Тайтл
     * script - Скрипты страницы
     * alert - Алерты страницы
     * navbar - Навигационная панель
     *
     *
     */

    public $title;


    public  function index($logined=false) //Главная страница сайта если пользователь незалогинен
    {

        $this->title='Политика Конфиденциальности'; // Записываем Тайтл нашей страницы

        return $this;
    }

}