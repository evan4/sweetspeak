<?php


namespace models;

use core\CRUD;

class FirsIndexSection
{
    private static function GetContent($params= null)
    {
        $materials_table = new CRUD('materials');
        $authors_table  = new CRUD('users');

        if(is_null($params)) { $offset=0;}

        else{$offset=$params;}
            settype($offset, 'integer');
            $materials_content=$materials_table->GetInfo(array('moderated'),null,'=',1,4,0,'ORDER BY likes ')->Resulting;
            $users_content=$authors_table->GetInfo()->Resulting;

            $result = array_merge(array_slice($materials_content, $offset), array_slice($materials_content, 0, $offset));

            $find_autor_id0=$result[0]['author_id'];
            $find_autor_id1=$result[1]['author_id'];
            $find_autor_id2=$result[2]['author_id'];
            $find_autor_id3=$result[3]['author_id'];
            foreach ($users_content as $user){
                if($user['id']==$find_autor_id0){// Первый автор
                    $name0=$user['name'];
                    $id0=$find_autor_id0;
                    $photo0=$user['photo'];
                }
                if($user['id']==$find_autor_id1){// Вторрой автор
                    $name1=$user['name'];
                    $id1=$find_autor_id1;
                    $photo1=$user['photo'];
                }
                if($user['id']==$find_autor_id2){// Третий автор
                    $name2=$user['name'];
                    $id2=$find_autor_id2;
                    $photo2=$user['photo'];
                }
                if($user['id']==$find_autor_id3){// Четвертый автор
                    $name3=$user['name'];
                    $id3=$find_autor_id3;
                    $photo3=$user['photo'];
                }
            }
                $humansDate= explode('-',$result[0]['date']);
                $postDate=$humansDate[2].'.'.$humansDate[1].'.'.$humansDate[0];
            $content = array
            (
                'main_photo'        =>  $result[0]['avatar'],
                'mini_top'          =>  $result[1]['ava_small'],
                'mini_center'       =>  $result[2]['ava_small'],
                'mini_last'         =>  $result[3]['ava_small'],
                'main_date'         =>  $postDate,
                'main_link_category'=>  $id0,
                'main_link_post'    =>  $result[0]['url'],
                'main_category_post'=>  strstr($result[0]['url'], '?', true),
                'main_author'       =>  $name0,
                'main_author_avatar'=>  $photo0,
                'main_title'        =>  $result[0]['title'],
                'main_category_name'=>  $result[0]['category_id'].'-'.$result[0]['subcategoryes'],
                'main_snippet'      =>  $result[0]['snippet'],
                'main_comments'     =>  $result[0]['comments_count'],
                'main_look'         =>  $result[0]['see'],
                'main_likes'        =>  $result[0]['likes'],
                'top_author_avatar' =>  $photo1,
                'top_title'         =>  $result[1]['title'],
                'top_link_post'     =>  $result[1]['url'],
                'top_id'            =>  $id1,
                'top_link_category' =>  $id1,
                'top_author'        =>  $name1,
                'top_look'          =>  $result[1]['see'],
                'top_comments'      =>  $result[1]['comments_count'],
                'top_likes'         =>  $result[1]['likes'],
                'center_author_avatar' => $photo2,
                'center_title'        => $result[2]['title'],
                'center_link_post'    => $result[2]['url'],
                'center_link_category'=>$id2,
                'center_author'      => $name2,
                'center_id'          => $id2,
                'center_look'        => $result[2]['see'],
                'center_comments'    => $result[2]['comments_count'],
                'center_likes'       => $result[2]['likes'],
                'last_author_avatar' => $photo3,
                'last_title'         => $result[3]['title'],
                'last_link_post'     => $result[3]['url'],
                'last_id'            => $id3,
                'last_link_category' => $id3,
                'last_author'        => $name3,
                'last_look'          => $result[3]['see'],
                'last_comments'      => $result[3]['comments_count'],
                'last_likes'         => $result[3]['likes'],
                'controls'           => $id1
            );

        return $content;
    }

    public static function SimpleGet($params = null)
    {
        if (is_null($params))
        {
           $content = self::GetContent();

        }
        else{
            $content = self::GetContent($params[0]['val']);
        }

        return array('main_photo'       =>  $content['main_photo'],
                     'mini_top'         =>  $content['mini_top'],
                     'mini_center'      =>  $content['mini_center'],
                     'mini_last'        =>  $content['mini_last'],

                    'main_date'         =>  $content['main_date'],
                    'main_category'     =>  $content['main_category'],
                    'main_snippet'      =>  $content['main_snippet'],
                    'main_author'       =>  $content['main_author'],
                    'main_author_avatar'=>  $content['main_author_avatar'],
                    'main_look'         =>  $content['main_look'],
                    'main_comments'     =>  $content['main_comments'],
                    'main_link_category'=>  $content['main_link_category'],
                    'main_category_post'=>  $content['main_category_post'],
                    'main_category_name'=>  $content['main_category_name'],
                    'main_link_post'    =>  $content['main_link_post'],
                    'main_title'        =>  $content['main_title'],
                    'main_likes'        =>  $content['main_likes'],
                    'top_title'         =>  $content['top_title'],
                    'top_author'        =>  $content['top_author'],
                    'top_author_avatar' =>  $content['top_author_avatar'],
                    'top_look'          =>  $content['top_look'],
                    'top_link_category' =>  $content['top_link_category'],
                    'top_comments'      =>  $content['top_comments'],
                    'top_link_post'     =>  $content['top_link_post'],
                    'top_likes'         =>  $content['top_likes'],
                    'center_title'      =>  $content['center_title'],
                    'center_author'     =>  $content['center_author'],
                    'center_author_avatar'=>$content['center_author_avatar'],
                    'center_look'       =>  $content['center_look'],
                    'center_link_category'=>$content['center_link_category'],
                    'center_comments'   =>  $content['center_comments'],
                    'center_link_post'  =>  $content['center_link_post'],
                    'center_likes'      =>  $content['center_likes'],
                    'last_title'        =>  $content['last_title'],
                    'last_author'       =>  $content['last_author'],
                    'last_author_avatar'=>  $content['last_author_avatar'],
                    'last_look'         =>  $content['last_look'],
                    'last_link_category'=>  $content['last_link_category'],
                    'last_comments'     =>  $content['last_comments'],
                    'last_link_post'    =>  $content['last_link_post'],
                    'last_likes'        =>  $content['last_likes']
                    );
    }
}