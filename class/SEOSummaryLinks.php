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
        $this->sitemap = $sitemap;
        
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
     * @public
     * @param string $url
     * @return \SEOSummaryLinks
     */
    public function setUrl($url) {
        $this->url = $url;
        
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
}
