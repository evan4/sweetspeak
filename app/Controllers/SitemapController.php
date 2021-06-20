<?php

namespace App\Controllers;

use samdark\sitemap\Sitemap;
use samdark\sitemap\Index;

use models\CategoryTable;
use models\AuthorModel;
use models\PostModel;

class SitemapController
{
    private $sitemap = ROOT .  DIRECTORY_SEPARATOR .'sitemap.xml';

    public function __construct()
    {
        $days = $this->getNumbersDaysSitemapModified();
        
        if($days > 7){
            
            $this->generateSitemap();
        }
        
    }

    public function generateSitemap()
    {
        $urls = [
            '/template/home/about.twig' => '/about',
            '/template/home/privacy.twig' => '/privacy',
            '/template/home/blog.twig' =>'/for_experts',
            '/template/home/rules.twig' => '/rules', 
        ];
        $limit = 3;

        $priority = 0.80;

        //file_put_contents($this->sitemap, "");
        // create sitemap
        
        $sitemap = new Sitemap($this->sitemap);
        
        $posts_model = new PostModel();

        $lastArticle = $posts_model->lastArticleDate();

        $sitemap->addItem(DOMAIN_NAME, strtotime($lastArticle['date']), Sitemap::WEEKLY, 1);
        $sitemap->addItem(DOMAIN_NAME.'/articles', strtotime($lastArticle['date']), Sitemap::WEEKLY, $priority);

        // static pages
        foreach ($urls as $key => $url) {
           
            $dateModified = $this->getFileDaysCreated($key);
            
            if($dateModified){
                $sitemap->addItem(DOMAIN_NAME.$url, $dateModified, Sitemap::WEEKLY, $priority);
            }

        }

        // categories pages
        $all_cateofires = CategoryTable::categoriesFormat();

     
        $parents = [];

        foreach ($all_cateofires['all'] as $category) {

            if($category['parent'] === 0){

                 $subcategopriesIds = [];

                foreach ($all_cateofires['all'] as $subcategory) {
                    if($subcategory['parent'] === $category['id']){
                        array_push($subcategopriesIds, $subcategory['id']);
                    }
                }

                $idsString = implode(',', $subcategopriesIds);
                
                $articleDate =  $posts_model->getLastArticleCreateDateByCategoriesId( $idsString );
                if($articleDate){
                    $sitemap->addItem(DOMAIN_NAME.'/'.$category['slug'],  strtotime($articleDate[0]['date']), Sitemap::WEEKLY, $priority);
                }
                $parents[$category['id']] = $category['slug'];
            }else{
                
                $articleDate =  $posts_model->getLastArticleCreateDateByCategoryId($category['id']);
                if($articleDate){
                    $sitemap->addItem(DOMAIN_NAME.'/'.$parents[$category['parent']].'/'.$category['slug'],  strtotime($articleDate[0]['date']), Sitemap::WEEKLY, $priority);
                }
            }

        }

        $author_model = new AuthorModel();
        $total = $author_model->getAuthorsNumbers();
        
        if($total > $limit){
            
            $authors_pages = intdiv( $total, $limit) + 2;
            
            for ($i=1; $i < $authors_pages; $i++) { 
                $offset = $i === 1 ? 0 : ($i - 1) * $limit;
                $authors = $author_model->getAuthors([
                    'offset' => $offset,
                    'limit' => $limit
                ]);
                
                foreach ($authors as $author) {

                    $articleDate = $posts_model->getLastArticleCreateDateByAuthorId($author['id']);
                    
                    if($articleDate){
                        $sitemap->addItem(DOMAIN_NAME.'/authors/articles/'.$author['id'],  strtotime($articleDate[0]['date']), Sitemap::WEEKLY, $priority);
                    }

                    $sitemap->addItem(DOMAIN_NAME.'/authors/'.$author['id'],  strtotime($author['updated_at']), Sitemap::WEEKLY, $priority);
                }
            }
        }else{
            $authors = $author_model->getAuthors([
                'offset' => 0,
                'limit' => $limit
            ]);
            
            foreach ($authors as $author) {
                
                $articleDate = $posts_model->getLastArticleCreateDateByAuthorId($author['id']);
                
                if($articleDate){
                    $sitemap->addItem(DOMAIN_NAME.'/authors/articles/'.$author['id'],  strtotime($articleDate[0]['date']), Sitemap::WEEKLY, $priority);
                }
                $sitemap->addItem(DOMAIN_NAME.'/authors/'.$author['id'],  strtotime($author['updated_at']), Sitemap::WEEKLY, $priority);
            }
        }
        
        $posts_total = $posts_model->getPostsNumbers();

        if($posts_total > $limit){
            $posts_pages = intdiv( $posts_total, $limit) + 2;
            for ($i=1; $i < $posts_pages; $i++) { 
                $offset = $i === 1 ? 0 : ($i - 1) * $limit;
                
                $posts = $posts_model->getPostsByLimit([
                    'offset' => $offset,
                    'limit' => $limit
                ]);
                
                foreach ($posts as $post) {
                    $sitemap->addItem(DOMAIN_NAME.'/article/'.$post['slug'], strtotime($post['updated_at']), Sitemap::WEEKLY, $priority);
                }
            }
        }else{
            $posts = $posts_model->getPostsByLimit([
                'offset' => 0,
                'limit' => $limit
            ]);

            foreach ($posts as $post) {
                $sitemap->addItem(DOMAIN_NAME.'/article/'.$post['slug'], strtotime($post['updated_at']), Sitemap::WEEKLY, $priority);
            }
        }



        // write it
        $sitemap->write();
    }

    private function getNumbersDaysSitemapModified()
    {
        $days = 0;
        
        if (file_exists($this->sitemap)) {
            
            $time_sitemap = filemtime($this->sitemap);
            
            $now = new \DateTime("now");
            $start_date = new \DateTime();

            $origin = $start_date->setTimestamp($time_sitemap);
            
            $interval = $origin->diff($now);
            $days = $interval->format('%d');
            
        }

        return $days;
    }

    private function getFileDaysCreated($file)
    {   
       
        if (file_exists(ROOT . $file)) {
            return filemtime( ROOT . $file);
        }

        return null;
    }
}
