<?php

//TODO DRY
namespace models;


use widgets\Pagination;
use widgets\Navbar;
use core\Model;
use core\CRUD;


class ShowRoomModel extends Model
{
    public $content;
    public $title;
    public $script;

    public $navbar;
    public $content_result;
    public $pagination;
    public $SearchPlaysholder;

    public function index($req_method,$params)
    {
            $maxcontent=include 'config/mat_on_page.php';
            $this->content_result.='
                <div class="container mt-5 wow zoomIn">
                    <div class="row mt-5">'; // Начало контентной части

            $containerIfno= new CRUD('materials',null);
            $TotalPages=ceil($containerIfno->TotalRows/$maxcontent); // Делим количество строк на количество материалов на странице



                    if (($req_method === 'AjaxSearch')) // Если кто-то хочет поискать в Аяксе
                    {
                        if ($params[0]['val'] === 'clear') // Если пришла команда сбросить все фильтры повторяем всё что было на начальной странице
                        {
                            if (isset($_SESSION['search'])) // Проверяем был ли установлен фильтр поика
                            {
                                unset($_SESSION['search']); // Уничтожаем фильтр
                            }
                            $containerIfno->GetInfo(null,null,null,null, $maxcontent,null,null); //Запрашиваем все данные из таблицы
                            $this->pagination= new Pagination(1,$TotalPages,'ShowRoom','page'); // Создаем пагинацию
                            foreach ($containerIfno->Resulting as $el){
                                $this->content_result.=$el['content'];
                            }

                            return $this;
                        }
                        else // Устанавливаем фильтр поиска
                        {
                            $_SESSION['search'] = $params[0]['val'];
                            $this->SearchPlaysholder = $_SESSION['search'];

                            $containerIfno->GetInfo(array('title','description','autor'),'OR','LIKE',$_SESSION['search'] ,$maxcontent,0,' AND ismoderate = 1');

                            $TotalPages = intdiv($containerIfno->TotalRows, $maxcontent); // Делим количество строк на количество материалов на странице

                            if($containerIfno->CurrentRows > 0){
                                foreach ($containerIfno->Resulting as $el){
                                    $this->content_result.=$el['content'];
                                }
                            }
                            else{
                                $this->content_result.='<div class="mt-5 text-center"><h2>Ничего не найдено</h2></div>';
                            }
                            $this->pagination= new Pagination(1,$TotalPages,'ShowRoom','page');
                        }
                        return $this;
                    }
                    else // Действия с Пагинацией
                    {

                        if(isset($_SESSION['search'])) // Если пользователь что-то искал
                        {
                            $offset=str_replace('page','',$req_method);
                            if(($offset==='index')){$offset=1;} // Если к нам просто зашли и был запрошен метод индекс, тогда показывааем первую страницу
                            $pagi_off=$offset; // Сохраняем значение отображенной страницы для пагинации
                            $this->SearchPlaysholder = $_SESSION['search']; // Записываем состояние строки поиска в строку
                            $offset=($offset-1)*$maxcontent; // Считаем сдвиг
                            $containerIfno->GetInfo(array('title','description','autor'),'OR','LIKE',$this->SearchPlaysholder,$maxcontent,$offset);
                            $RowsCountAjax = $containerIfno->TotalRows; // Количество строк при поиске составило
                            $TotalPages = intdiv($RowsCountAjax, $maxcontent); // Делим количество строк на количество материалов на странице
                            if($containerIfno->CurrentRows > 0){ // Если есть что показывать то в цикле формируем контент
                                foreach ($containerIfno->Resulting as $el){ // Формирование контента
                                    $this->content_result.=$el['content'];
                                }
                            }
                            else{ // Если показывать нечего, то сообщаем об этом следующим образом
                                $this->content_result.='<div class="mt-5 text-center"><h2>Ничего не найдено</h2></div>';
                            }
                            $this->pagination= new Pagination($pagi_off,$TotalPages,'ShowRoom','page'); //Строим пагинацию

                        }
                        else{
                            $offset=str_replace('page','',$req_method);
                            if(!is_numeric($offset)){$offset=1;}
                            $pagi_off=$offset;
                            $offset=($offset-1)*$maxcontent;
                            $containerIfno->GetInfo(null,null,null, null,$maxcontent,$offset,null); //Запрашиваем все данные из таблицы
                            if($containerIfno->CurrentRows > 0){
                                foreach ($containerIfno->Resulting as $el){
                                    $this->content_result.=$el['content'];
                                }
                            }
                            else{
                                $this->content_result.='Ничего не найдено';
                            }
                            $this->pagination= new Pagination($pagi_off,$TotalPages,'ShowRoom','page'); // Создаем пагинацию

                        }

                    }


            $this->content_result.=' 
                    </div>
                </div>'; // Конец контентной части

        $this->content_result.=$this->pagination->pagi;
        if(isset($_SESSION['User_info'])){
            $this->navbar = Navbar::TestNav('Работы',$this->pages,true,'',$this->SearchPlaysholder); // передаем на построение верхнее меню 1-Активная страница ИМЯ 2-Массив страниц 3. Залогинены / нет 4. Алерт если он есть
        }
        else{
        $this->navbar = Navbar::TestNav('Работы',$this->pages,false,'',$this->SearchPlaysholder); // передаем на построение верхнее меню 1-Активная страница ИМЯ 2-Массив страниц 3. Залогинены / нет 4. Алерт если он есть
        }


        $this->title=$req_method;
        $this->content=$this->loginModal;
        $this->script='';

    }

    public function author($target){
       $authors_table = new CRUD('users');
       $author=$authors_table->GetInfo(array('id'),null,'=',$target,null,0,null)->Resulting;
       $this->photo = $author[0]['photo'];
       $this->author_id = $target;
       $this->title='Автор SweetSpeak '.$author[0]['name'];
    }

    public function GetPost($target){

        $post_table= new CRUD ('materials');
        $this->post = $post_table->GetInfo(array('id'),null,'=',$target,null,0,null)->Resulting[0];

        $authors_table= new CRUD('users');
        $this->author = $authors_table->GetInfo(array('id'),null,'=',$this->post['author_id'],null,0,null)->Resulting[0];

    }
    public function GetCategory($target){

        $category_table= new CRUD('materials');
        $this->category_content = $category_table->GetInfo(array('category_id'),null,'=',$target,null,0,null)->Resulting;

    }

}