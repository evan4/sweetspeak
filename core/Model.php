<?php


namespace core;


class Model
{
    public function extendPostData(array $posts)
    {
        $categories = $_SESSION['all_categories'];
        
        $image_path = '/bundles/articles/';

        $now = new \DateTime("today");
        
        for($i=0; $i < count($posts) ; $i++) {
            
            $posts[$i]['date'] = $this->dateFormat($posts[$i]['date'], $now);
            $posts[$i]['snippet'] = strip_tags($posts[$i]['snippet']);
           
            $posts[$i]['photo'] = [
                'large' =>$image_path.'1000_'.$posts[$i]['photo'],
                'medium' =>$image_path.'300_'.$posts[$i]['photo'],
            ];
            $posts[$i]['author_url'] = '/authors/'.$posts[$i]['author_id'];
            
            foreach ($categories['all'] as $category) {
                
                if($category['id'] == $posts[$i]['categories_id']){
                    $subcategory_post = $category;
                    break;
                }
            }
            
            $categpry_id = array_search($subcategory_post['parent'], array_column($categories['all'], 'id'));
            
            $posts[$i]['url'] = '/article/'.$posts[$i]['slug'];
            $posts[$i]['category'] = $subcategory_post['name'];
        }
       
        return $posts;
    }

    public function dateFormat($date, $now)
    {
        $now = new \DateTime("now");

        $origin = new \DateTime($date);
        
        $interval = $origin->diff($now);
        
        $days = $interval->format('%d');
        $hours = $interval->format('%h');

        $date_format = '';

        if($days){
            $date_format = date("d-m-Y", strtotime($date));
        }else{

            switch ($hours) {
                case  0:
                    $date_format = 'менее часа назад';
                    break;
                case  1:
                case  21:
                    $date_format = $interval->format('%h час назад');
                    break;
                case  2:
                case  3:
                case  4:
                case  22:
                case  23:
                    $date_format = $interval->format('%h часа назад');
                    break; 
                default:
                    $date_format = $interval->format('%h часов назад');
                    break;
            }
            
        }
        return $date_format;
    }

    public function checkAjax(): bool
    {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            return true;
        }
        
        return false;
    }
}