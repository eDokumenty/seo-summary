<?php

/**
 * Description of SEOSummaryLinks
 * 
 * @package SEO Summary
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
     * @var array[SEOSummaryLinksContent]
     */
    private $content = [];
    
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
     * @global wpdb $wpdb
     * @param integer|null $id
     * @param string $sitemap
     * @param string $url
     * @param integer $countWors
     */
    public function __construct($id, $sitemap, $url, $countWors) {
        if (is_null($id)) {
            $this->ID = null;
        } else {
             $this->ID = intval($id);
        }
        
        $this->sitemap = str_replace([home_url(), '-sitemap.xml', 'http://edokumenty.eu/'], '', $sitemap);
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
        $this->count_words = intval($countWors);
    }
    
    /**
     * 
     * @public
     * @return integer
     */
    public function getId(){
        return $this->ID;
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
     * @return array[SEOSummaryLinksContent]
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * 
     * @public
     * @param array $content
     * @return \SEOSummaryLinks
     */
    public function setContent($content) {
        $this->content = $content;
        
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
}
