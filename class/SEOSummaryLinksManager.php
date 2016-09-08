<?php

/**
 * Description of SEOSummaryLinksManager
 * 
 * @package SEO Summary
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
     *
     * @var string
     */
    private $subTabnam;
    
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
        $this->subTabnam = $prefix.'seo_summary_links_content';
    }
    
    
    
    /**
     * Activate plugin
     * 
     * @public
     */
    public function install(){
        $version = SEO_VERSION;
        
        $query = "CREATE TABLE ".$this->tabnam." ("
                . " ID int(9) NOT NULL AUTO_INCREMENT,"
                . " sitemap VARCHAR(50),"
                . " url VARCHAR(250),"
                . " post_name VARCHAR(50),"
                . " count_words int(9),"
                . " PRIMARY KEY (ID) "
                . ")";
        
        $query2 =  "CREATE TABLE ".$this->subTabnam." ("
                . " ID int(9) NOT NULL AUTO_INCREMENT,"
                . " summary_id int(9),"
                . " url VARCHAR(250),"
                . " post_name VARCHAR(50),"
                . " PRIMARY KEY (ID),"
                . " CONSTRAINT fk_summary_id FOREIGN KEY (summary_id) REFERENCES {$this->tabnam}(ID)"
                . ")";
        
                
        
        if ($this->wpdb->get_var("SHOW TABLES LIKE  '".$this->tabnam."'") != $this->tabnam) {
           
            
            $this->wpdb->query($query);
            $this->wpdb->query($query2);
            add_option('seo-summary_version', $version);
            add_option('seo-summary_speed', '2000');
            add_option('seo-summary_type', 'vertical');
        }
        
        $versionClient = get_option('seo-summary_version', $version);
        if (version_compare('1.0.1', $versionClient, '>=')) {
            $this->uninstall();
            
            $this->wpdb->query($query);
            $this->wpdb->query($query2);
        }
        
        
        update_option('seo-summary_version', $version);        
    }
    
    /**
     * Update plugin
     * 
     * @public
     */
    public function update() {
        $versionClient = get_option('seo-summary_version', SEO_VERSION);

        //update for version 1.1.1
        if (version_compare('1.1.1', $versionClient) === 1) {
            $query = "ALTER TABLE `{$this->subTabnam}` ADD `xpath` TEXT NULL AFTER `post_name`;";
            
            $this->wpdb->query($query);
        }
        
        update_option('seo-summary_version', SEO_VERSION);   
    }
    
    

    /**
     * 
     * @public
     * @param SEOSummaryLinks $seo
     */
    public function persist(SEOSummaryLinks $seo){
        $this->container[] = $seo;
    }
    
    /**
     * 
     * @public
     * @param boolean $insert
     * @return int
     */
    public function flush($insert = true) {
        if ($insert) {
            $row = 0;
            foreach($this->container as $seo) {
                $data = $this->prepare($seo);
                
                $this->wpdb->insert($this->tabnam, $data, [
                    '%s', '%s', '%s', '%s'
                ]);
                
                
                $summary_id = $this->wpdb->insert_id;
                
                $seoContents = $seo->getContent();

                foreach ($seoContents as $seoContent) {
                    $seoContent->setSummaryId($summary_id);
                    $subData = $this->prepareSub($seoContent);
                    
                    $this->wpdb->insert($this->subTabnam, $subData, [
                        '%s', '%s', '%s'
                    ]);
                    $row++;
                }

                $row++;
            }
            
        } 
        return $row;
    }
    
    
    /**
     * Truncate table
     * 
     * @public
     */
    public function truncate(){
        $query ='DELETE FROM '.$this->subTabnam;
        $this->wpdb->query($query);
        $query ='DELETE FROM '.$this->tabnam;
        $this->wpdb->query($query);
        
       
        $query = 'ALTER TABLE '.$this->tabnam.' AUTO_INCREMENT=1 ';
        $this->wpdb->query($query);
        $query = 'ALTER TABLE '.$this->subTabnam.' AUTO_INCREMENT=1 ';
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
            'post_name' => $seo->getPostName(),
            'count_words' => $seo->getCount_words(),
        ];
    }
    
    /**
     * 
     * @param SEOSummaryLinksContent $seo
     * @return array
     */
    protected function prepareSub(SEOSummaryLinksContent $seo) {
        return [
            'summary_id' => $seo->getSummaryId(),
            'url' => $seo->getUrl(),
            'post_name' => $seo->getPostName(),
            'xpath' => $seo->getXPath(),
        ];
    }


    
    /**
     * 
     * @public
     * @param string $postname
     * @return string
     */
    public function countAllLinkOnPage($postname) {
        $query = "SELECT s.url, count(*) as replay FROM {$this->subTabnam} as s, {$this->tabnam} as t WHERE t.post_name = '$postname' and t.ID = s.summary_id GROUP BY s.url";
        
        $data = $this->wpdb->get_results($query, ARRAY_A);
        
        $count = 0;
        foreach ($data as $row){
            $count++;
        }
        return $count;
    }
    
    /**
     * 
     * @public
     * @param string $postname
     * @return string
     */
    public function countAllLinkCallToPage($postname) {
        $query = "SELECT t.url, count(*) as replay FROM {$this->subTabnam} as s, {$this->tabnam} as t WHERE s.post_name = '$postname' and t.ID = s.summary_id GROUP BY t.url";
        
        $data = $this->wpdb->get_results($query, ARRAY_A);
        
        $count = 0;
        foreach ($data as $row){
            $count++;
        }
        return $count;
    }
    
    /**
     * 
     * @public
     * @param string $postname
     * @return string
     */
    public function getCountWords($postname) {
        $query = "SELECT count_words FROM {$this->tabnam} WHERE post_name = '$postname'";
        $count = $this->wpdb->get_var($query);
        
        return (!empty($count)) ? $count : '0';
    }
    
    /**
     * 
     * @public
     * @param string $postname
     * @return array
     */
    public function getAllLinkOnPage($postname) {
        $query = "SELECT s.url, count(*) as replay FROM {$this->subTabnam} as s, {$this->tabnam} as t WHERE t.post_name = '$postname' and t.ID = s.summary_id GROUP BY s.url";
        
        return $this->wpdb->get_results($query, ARRAY_A);
    }
    
    /**
     * 
     * @public
     * @param string $postname
     * @return array
     */
    public function getAllLinkCallToPage($postname) {
        $query = "SELECT t.url, count(*) as replay FROM {$this->subTabnam} as s, {$this->tabnam} as t WHERE s.post_name = '$postname' and t.ID = s.summary_id GROUP BY t.url";
        
        return $this->wpdb->get_results($query, ARRAY_A);
    }
    
    /**
     * 
     * @public
     * @param string $postname
     * @return string
     */
    public function getUrl($postname) {
        $query = "SELECT url FROM {$this->tabnam} WHERE post_name = '$postname'";
        $url = $this->wpdb->get_var($query);
        
        return (!empty($url)) ? $url : '';
    }
    
    /**
     * 
     * @public
     * @param integer $id
     * @return string
     */
    public function getXpath($id) {
        $id = intval($id);
        $query = "SELECT xpath FROM {$this->subTabnam} WHERE ID = $id";
        $x = $this->wpdb->get_var($query);
        
        return (!empty($x)) ? $x : '';
    }
    
    /**
     * 
     * @public
     * @param integer $id
     * @return string
     */
    public function getUrlParent($id) {
        $id = intval($id);
        $query = "SELECT t.url FROM {$this->subTabnam} as s, {$this->tabnam} as t WHERE s.ID = $id and t.ID = s.summary_id ";
        $x = $this->wpdb->get_var($query);
        
        return (!empty($x)) ? $x : '';
    }
    
    /**
     * Deactivate plugin
     * 
     * @public
     */
    public function uninstall() {
        $query ='DROP TABLE '.$this->subTabnam;
        $query2 ='DROP TABLE '.$this->tabnam;
        $this->wpdb->query($query);
        $this->wpdb->query($query2);
    }
}
