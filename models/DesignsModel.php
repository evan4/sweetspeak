<?php


namespace models;


use core\CRUD;

class DesignsModel
{

    public function index()
    {
        $this->title="Дизайны";
    }
    public function new_design($params){
        $raskladka[0]['name']="Раскладка 1";
        $raskladka[0]['activation']=true;
        $raskladka[0]['materials']= array(array('name'=>'Новая плитка','artiqle'=>'Новый артикул','width'=>'0','height'=>'0','pieces'=>'0','m'=>'0','price'=>'0','photo'=>''));
        $info['raskladka']=$raskladka;
        $inform = new CRUD('designs');
        $inform->Add(array('name'=>$params[0]['val'],'info'=>serialize($info)));
        $table = $inform->GetInfo()->Resulting;
        for($i = 0, $iMax=count($table); $i<$iMax; $i++ )
        {
            $table[$i]['info']=unserialize($table[$i]['info']);
            $table[$i]['app_info']=unserialize($table[$i]['app_info']);
        }
        header('Location: /Admin/Designs');
    }
}