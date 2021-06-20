<?php


namespace models;

use core\CRUD;
class UsersModel
{
        public function index(){
            $this->title="Управление пользователями";
        }

        public function getallusers(){
            $base = new CRUD('users');
            $this->Result=$base->GetInfo()->Resulting;
        }
        public function block($id){
            $base =  new CRUD ('users');
            $base->GetInfo(array('id'),null,'=',$id,null,null,null);
            $user=$base->Resulting[0]['login'];
            if($base->Resulting[0]['status'] =='1'){ // если пользователь был активным, то пытаемся перевести его в режим заблокирован
                $base->Update(array('status'=>0),null,'=',array('id'=>$id));
               $this->response='Пользователь '.$user.' был заблокирован';
            }
            else{
                $base->Update(array('status'=>1),null,'=',array('id'=>$id));
                $this->response='Пользователь '.$user.' активен';
            }
        }
        public function delete($id){
            $base = new CRUD('users');
            $target = $base->GetInfo(array('id'),null,'=',$id,null,null,null)->Resulting[0]['login'];
            $base->Delete(array('id'=>$id),null,'=');
            $this->Result = $base->GetInfo()->Resulting;
            $this->message ='Пользователь  был удален';
            $base = new CRUD('log');
            $base->Delete(array('tablet'=>$target),null,'=');

        }
        public function edituser($params){
            $base = new CRUD('users');
            if(isset($params[6]['val'])){ // Если пришел новый пароль
               $new_pass = password_hash($params[6]['val'],PASSWORD_DEFAULT);
               $base->Update(array('login'=>$params[2]['val'],'password'=>$new_pass,'adress'=>$params[3]['val'],'city'=>$params[4]['val'],'geo'=>$params[5]['val']),null,'=',array('id'=>$params[1]['val']));
            }
            else{ // Если просто обновляем данные
                $base->Update(array('login'=>$params[2]['val'],'adress'=>$params[3]['val'],
                'city'=>$params[4]['val'],'geo'=>$params[5]['val']),null,'=',
                array('id'=>$params[1]['val']));
            }
            $this->Result = $base->GetInfo()->Resulting;
            $this->message ='Информаци о пользователе '.$base->GetInfo()->Resulting[0]['login'].' была сохранена';

        }
        public function adduser($params){
            $base = new CRUD('users');
            if(count($params)==6){ // Если все данные заполнены
                $new_pass = password_hash($params[5]['val'],PASSWORD_DEFAULT);
                $base->Add(array('login'=>$params[1]['val'],'password'=>$new_pass,'adress'=>$params[2]['val'],'city'=>$params[3]['val'],'geo'=>$params[4]['val'],'status'=>1));
                $this->message ='Пользователь был добавлен';
            }
            else{ // Если просто обновляем данные
                $this->message ='Не все необходимые поля были заполнены';
            }
            $this->Result = $base->GetInfo()->Resulting;


        }
        // Работа с прайсами

    public function Prices($user){ // Самое начало
            $base = new CRUD('users');
            $CurentUser = $base->GetInfo(array('login'),null,'=',$user,null,null,null)->Resulting[0];
            $this->title="Цены на плитку в магазине ".$CurentUser['city'].' '.$CurentUser['geo'];
    }

    public function AjaxPricesInit($target){
     $base = new CRUD('users');
     $CurentUser = $base->GetInfo(array('login'),null,'=',$target,null,null,null)->Resulting[0];
     $designs = unserialize ($CurentUser['designs']);
     $design_keys=array_keys($designs);
     $design_base = new CRUD('designs');
     $designs_data = $design_base->GetInfo()->Resulting;
     foreach ($design_keys as $val) {
            for($i = 0,$iMax=count($designs_data); $i < $iMax; $i++){
                if($designs_data[$i]['name']==$val){
                    $response['name']=$designs_data[$i]['name'];
                    $response['data']=unserialize($designs_data[$i]['info']);
                }
                $response_data[]=$response;
            }

     }
     $this->designs=$response_data;
     }

     public function getuserById(int $id)
     {
        $base = new CRUD('users');
        return $base->GetInfo(array('id'),null,'=',$id,null,null,null)->Resulting[0];
     }

     public function activate(int $id)
     {
        $base = new CRUD('users');
        $base->Update(
            [
                'confirm' => 1,
                'remember_token' => '',
            ],
            null,
            '=',
            ['id' => $id]
        );

     }

     public function updatePassword(array $data)
     {
        $base = new CRUD('users');
        $base->Update(
            [
                'password' => password_hash( 
                    $data['password'],  
                    PASSWORD_DEFAULT
                ),
                'remember_token' => '',
                'verify_token_timestamp' => null
            ],
            null,
            '=',
            ['id' => $data['id']]
        );
     }
}