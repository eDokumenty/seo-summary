<?php

/**
 * Description of SEOSummaryLinksManager
 * 
 * @package SEO Summary
 * @since 
 * @version 0.1
 * @author Piotr Kuźnik <piotr.damian.kuznik@gmail.com>
 * @license gpl
 * @copyright (c) eDokumenty Sp. z o.o
 */
class SEOSummaryLinksManager {

    /**
     *
     * @var wpdb
     */
    private $wpdb;

    /**
     *
     * @var string
     */
    private $tabnam;
    
    /**
     * @var array[SEOSummaryLinks]
     */
    private $container = [];

    /**
     * 
     * @global wpdb $wpdb
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $prefix = $this->wpdb->prefix;
        $this->tabnam = $prefix.'seo_summary_links';
    }
    
    
    
    /**
     * Activate plugin
     * 
     * @global wpdb $wpdb
     */
    public function install(){
        $version = '0.1';
        
        if ($this->wpdb->get_var("SHOW TABLES LIKE  '".$this->tabnam."'") != $this->tabnam) {
            
            //create
            $query = "CREATE TABLE ".$this->tabnam." ("
                    . " ID int(9) NOT NULL AUTO_INCREAMENT,"
                    . " sitemap VARCHAR(50)  NOT NULL,"
                    . " url VARCHAR(250) NOT NULL,"
                    . " content_url VARCHAR(250) NOT NULL,"
                    . " PRIMARY KEY (ID) "
                    . ")";
            
            $this->wpdb->query($query);
            
            add_option('seo-summary_version', $version);
            add_option('seo-summary_speed', '2000');
            add_option('seo-summary_type', 'vertical');
        }
    }
    
    /**
     * Deactivate plugin
     * 
     * @global wpdb $wpdb
     */
    public function uninstall() {
        $query ='DROP TABLE '.$this->tabnam;
        $this->wpdb->query($query);
    }

    /**
     * 
     * @public
     * @param SEOSummaryLinks $seo
     */
    public function persist(SEOSummaryLinks $seo){
        $this->container[] = $seo;
    }
    
    
    public function flush() {
        $query = 'INSERT INTO '.$this->tabnam.' (sitemap, url, content_url) VALUES ';
        
        $row = 0;
        
        foreach($this->container as $seo) {
            $row_value = array_values($this->prepare($seo));
            $sql = '(';
            foreach ($row_value as $val) {
                $sql .= "'$val'";
            }
            if (count($this->container) == 1 || $row - 1 == count($this->container)) {
                $sql .= ')';
            } else {
                $sql .= '), ';
            }
            
            $query .= $sql;
            
            $row++;
        }
        $this->wpdb->query($query);
    }
    
    /**
     * 
     * @param SEOSummaryLinks $seo
     * @return array
     */
    protected function prepare(SEOSummaryLinks $seo) {
        
        return [
            'sitemap' => $seo->getSitemap(),
            'url' => $seo->getUrl(),
            'content_url' => $seo->getContent_url(),
        ];
    }
    
    


    /**
     * Truncate table
     * 
     * @public
     */
    public function truncate(){
        $query ='TRUNCATE TABLE '.$this->tabnam;
        $this->wpdb->query($query);
    }
}