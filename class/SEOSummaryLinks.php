<?php

/**
 * Description of SEOSummaryLinks
 * 
 * @package SEO Summary
 * @since 
 * @version 0.1
 * @author Piotr Kuźnik <piotr.damian.kuznik@gmail.com>
 * @license default
 * @copyright (c) eDokumenty Sp. z o.o
 */
class SEOSummaryLinks {
    
    /**
     *
     * @var integer
     */
    private $ID;
    
    
    /**
     * @var string
     */
    private $sitemap;
    
    /**
     * @var string
     */
    private $url;
    
    /**
     * @var string
     */
    private $content_url;
    
    /**
     *
     * @var integer
     */
    private $count_words;
    
    /**
     *
     * @var string
     */
    private $post_name;
    
    /**
     * 
     * @public
     * @return integer
     */
    public function getId(){
        return $this->ID;
    }
    
    /**
     * 
     * @public
     * @param integer $id
     * @return \SEOSummaryLinks
     */
    public function setId($id){
        $this->ID = intval($id);
        
        return $this;
    }
    
    /**
     * @public
     * @return string
     */
    public function getSitemap() {
        return $this->sitemap;
    }
    
    /**
     * 
     * @public
     * @param string $sitemap
     * @return \SEOSummaryLinks
     */
    public function setSitemap($sitemap) {
        
        $this->sitemap = str_replace([home_url(), '-sitemap.xml', 'http://edokumenty.eu/'], '', $sitemap);
        
        return $this;
    }
    
    
    /**
     * 
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }
    
    /**
     * 
     * @return string
     */
    public function getPostName() {
        return $this->post_name;
    }
    
    /**
     * 
     * @public
     * @param string $url
     * @return \SEOSummaryLinks
     */
    public function setUrl($url) {
        $this->url = $url;
        
        $path = explode('/', $url);
        $post_name = (!empty($path[ count($path) - 1])) ? $path[ count($path) - 1] : $path[ count($path) - 2];
        
        $path2 = home_url();
        $home_url = (!empty($path2[ count($path2) - 1])) ? $path2[ count($path2) - 1] : $path2[ count($path2) - 2];
        if ($post_name == 'edokumenty.eu' ||  $home_url == $post_name) {
            
            global $wpdb;
            $prefix = $wpdb->prefix;
            $pageOnFront = $wpdb->get_var('SELECT option_value FROM '.$prefix.'options  WHERE option_name = \'show_on_front\'');
           
            if ($pageOnFront == 'page') {
                $slug = $wpdb->get_var('SELECT p.post_name FROM '.$prefix.'options op, '.$prefix.'posts p WHERE op.option_name = \'page_on_front\' and op.option_value = p.ID ');
                
                if (!empty($slug)) {
                    $post_name = $slug;
                }
                
            }
        }
        
        $this->post_name = $post_name;
        
        return $this;
    }
    
    /**
     * 
     * @public
     * @return string
     */
    public function getContent_url() {
        return $this->content_url;
    }
    
    /**
     * 
     * @public
     * @param string $content_url
     * @return \SEOSummaryLinks
     */
    public function setContent_url($content_url) {
        $this->content_url = $content_url;
        
        return $this;
    }
    
    /**
     * 
     * @public
     * @return integer
     */
    public function getCount_words() {
        return $this->count_words;
    }
    
    /**
     * 
     * @public
     * @param integer $count_words
     * @return \SEOSummaryLinks
     */
    public function setCount_words($count_words) {
        $this->count_words = $count_words;
        
        return $this;
    }
}
