<?php


namespace models;
use core\CRUD;

class BlockContentAjax
{
    public static function GetBlock($Block,$Act,$Template = null){
        $mod = new CRUD('materials');
        $users = new CRUD('users');
        $Usrs = $users->GetInfo(array('id'),null,'=',null,null,0,null)->Resulting; // получили пользователей
        if ($Act == 'init'){
            $infoAll=$mod->GetInfo(array('category_id'),null,'=',$Block,6,0,'ORDER BY date DESC')->Resulting;
            for($i=0,$Imax=count($infoAll);$i<$Imax; $i++){
                foreach ($Usrs as $user){
                    if($user['id']==$infoAll[$i]['author_id']){
                        $infoAll[$i]['User_id']=$user['id'];
                        $infoAll[$i]['User_photo']=$user['photo'];
                        $infoAll[$i]['UserName']=$user['name'];
                    }
                }
                if($infoAll[$i]['moderated']!=='1'){
                    unset($infoAll[$i]);
                }
            }
                foreach ($infoAll as $post){
                    $Result[]=$post;
                }

        }
        else{
            $infoAll = $mod->GetInfo(array('subcategoryes'),null,'=',$Act,6,0,'ORDER BY date DESC')->Resulting;
            for($i=0,$Imax=count($infoAll);$i<$Imax; $i++){
                foreach ($Usrs as $user){
                    if($user['id']==$infoAll[$i]['author_id']){
                        $infoAll[$i]['User_id']=$user['id'];
                        $infoAll[$i]['User_photo']=$user['photo'];
                        $infoAll[$i]['UserName']=$user['name'];
                    }
                }
                if($infoAll[$i]['moderated']!=='1'){
                    unset($infoAll[$i]);
                }
            }
            foreach ($infoAll as $post){
                $Result[]=$post;
            }
        }




        return $Result;
    }
}