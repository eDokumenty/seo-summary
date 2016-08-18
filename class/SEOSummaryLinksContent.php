<?php

/**
 * Description of SEOSummaryLinksContent
 * 
 * @package SEO Summary
 * @author Piotr Kuźnik <piotr.damian.kuznik@gmail.com>
 * @license default
 * @copyright (c) eDokumenty Sp. z o.o
 */
class SEOSummaryLinksContent {
    
    /**
     *
     * @var integer
     */
    private $ID;
    
    /**
     *
     * @var integer
     */
    private $summary_id;
    
    /**
     *
     * @var string
     */
    private $url;
    
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
     * @return \SEOSummaryLinksContent
     */
    public function setId($id){
        $this->ID = intval($id);
        
        return $this;
    }
    
    /**
     * 
     * @return integer
     */
    public function getSummaryId() {
        return $this->summary_id;
    }
    
    /**
     * 
     * @param integer $id
     * @return \SEOSummaryLinksContent
     */
    public function setSummaryId($id) {
        $this->summary_id = $id;
        
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
     * @return \SEOSummaryLinksContent
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
}
