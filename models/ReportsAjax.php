<?php


namespace models;

use core\CRUD;

class ReportsAjax
{
    public static function GetReport($params = null)
    {
        $db = new CRUD('log');
        return $db->GetInfo(null,null,null,null,null,0)->Resulting;
    }
}