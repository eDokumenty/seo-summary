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

        
    }

}
