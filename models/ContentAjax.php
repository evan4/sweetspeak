<?php


namespace models;

use widgets\ContentCentral;
use models\UsersActions;

class ContentAjax
{
    public static function GetContent($page)
    {
        if(isset($_SESSION['User_info'][0]['status'])){

        if($page==='index') {
            echo json_encode( array
            (   'navbar'=>Navbar::GetNav(true),
                'center'=>ContentCentral::index(),
                'bottom_center'=>array(1,2,3),
                'bottom_left'=>array('text'=>'Для ванной комнаты','gabarit'=>'(0,0м х 0,0м ) 6 м','price'=>'25 109'),
                'video'=>'/Content/1/Start.html',
            ));
            }
        elseif($page==='Возврат в меню'){ // Клиент сам вернулся в меню
            echo json_encode( array(
                'navbar'=>Navbar::GetNav(true),
                'center'=>ContentCentral::index(),
                'bottom_center'=>array(1,2,3),
                'bottom_left'=>array('text'=>'Для ванной комнаты','gabarit'=>'(0,0м х 0,0м ) 6 м','price'=>'25 109'),
                'video'=>'/Content/2/Start.html',
            ));
            UsersActions::makelog('Клиент вернулся в меню');
        }
        elseif($page === 'Расчитать'){
            echo json_encode( array( // клиент нажал на какую-то кнопку
                'navbar'=>Navbar::GetNav(true),
                'center'=>'',
                'bottom_center'=>array(1,2,3),
                'bottom_left'=>array('text'=>'Цель достигнута','price'=>'25 109'),
                'video'=>'/Content/1/Start.html',
            ));
            UsersActions::makelog('Клиент достиг цели');
        }
        else{
                echo json_encode( array( // клиент нажал на какую-то кнопку
                    'navbar'=>Navbar::GetNav(true),
                    'center'=>'',
                    'bottom_center'=>array(1,2,3),
                    'bottom_left'=>array('text'=>'Для ванной комнаты '.$page,'gabarit'=>'(0,0м х 0,0м ) 6 м','price'=>'25 109'),
                    'video'=>'/Content/2/Start.html',
                ));
            UsersActions::makelog('Клиент выбрал вариант '.$page);
            }
        }

    }

}