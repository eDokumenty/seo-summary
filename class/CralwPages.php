<?php

/**
 * Description of CralwPages
 * 
 * @package SEO Summary
 * @since 
 * @version 0.1
 * @author Piotr Kuźnik <piotr.damian.kuznik@gmail.com>
 * @license gpl
 * @copyright (c) eDokumenty Sp. z o.o
 */
class CralwPages {

    /**
     *
     * @var wpdb
     */
    private $wpdb;


    /**
     * 
     * @param wpdb $wpdb
     */
    public function __construct(wpdb $wpdb) {
        $this->wpdb = $wpdb;
        
        
    }
    
    /**
     * 
     * @param wpdb $wpdb
     */
    public function install(){
        $prefix = $this->wpdb->prefix;
        $tabnam = $prefix.'seo_summary_links';
        
        $version = '0.1';
        
        if ($this->wpdb->get_var("SHOW TABLES LIKE  '$tabnam'") != $tabnam) {
            
            //create
            $query = "CREATE TABLE $tabnam ("
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

}
