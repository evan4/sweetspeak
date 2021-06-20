<?php

namespace App\Controllers\Admin;

use Mycms\Controller;

class AdminController extends Controller
{
    protected $user;
    public $role;
    protected $limit = 10;
    protected $imageSize = [1000, 300];

    public function __construct()
    {
      
      parent::__construct();
      
      if(isset($_SESSION['User_info']) && !empty($_SESSION['User_info'])){
        $this->user = $_SESSION['User_info'];
        
        $this->role = isset($_SESSION['User_info']['status']) ? $_SESSION['User_info']['status'] : 'user';
      }else{
        $this->echoForbittenMessage();
      }
      
    }

    public function deleteImages($photo, $dir)
    {
      for ($i=0; $i < count($this->imageSize); $i++) { 
        $photoOld = BUNDLES.$dir.DIRECTORY_SEPARATOR.$this->imageSize[$i].'_'.
          $this->sanitizeText($photo);
          
          if (file_exists($photoOld)) {
            unlink($photoOld);
          }
       
      }
    }

    public function deleteImage($photo, $dir)
    {
        $photoOld = BUNDLES.$dir.DIRECTORY_SEPARATOR.$this->sanitizeText($photo);
          
        if (file_exists($photoOld)) {
          unlink($photoOld);
        }
       
    }

}
