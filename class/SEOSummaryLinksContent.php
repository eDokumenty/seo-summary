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
     * @var string
     */
    private $xpath;
    
    /**
     * 
     * @public
     * @global wpdb $wpdb
     * @param integer|null $id
     * @param integer|null $summary_id
     * @param string $url
     * @param string $xpath
     */
    public function __construct($id, $summary_id, $url, $xpath) {
        if (is_null($id)) {
            $this->ID = null;
        } else {
            $this->ID = intval($id);
        }
        
        if (is_null($summary_id)) {
            $this->summary_id = null;
        } else {
            $this->summary_id = intval($summary_id);
        } 
       
        
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
        $this->xpath = $xpath;
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
     * 
     * @public
     * @return integer
     */
    public function getSummaryId() {
        return $this->summary_id;
    }
    
    /**
     * 
     * @public
     * @param integer $summary_id 
     * @return \SEOSummaryLinksContent
     * @throws Exception
     */
    public function setSummaryId($summary_id) {
        if (!is_null($this->summary_id) ){
            throw new Exception(__CLASS__.' is set summary_ID');
        }
        $this->summary_id = intval($summary_id);
        
        return $this;
    }
    
    /**
     * 
     * @public
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }
    
    /**
     * 
     * @public
     * @return string
     */
    public function getPostName() {
        return $this->post_name;
    }

    /**
     * 
     * @public
     * @return string
     */
    public function getXPath() {
        return $this->xpath;
    }
}
