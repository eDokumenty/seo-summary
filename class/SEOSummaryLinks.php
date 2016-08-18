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
        $this->post_name = $path[ count($path) - 1];
        
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
