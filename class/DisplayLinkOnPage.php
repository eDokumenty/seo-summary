<?php

/**
 * Description of DisplayLinkOnPage
 * 
 * @package SEO Summary
 * @since 
 * @version 0.1
 * @author Piotr Kuźnik <piotr.damian.kuznik@gmail.com>
 * @license gpl
 * @copyright (c) eDokumenty Sp. z o.o
 */
class DisplayLinkOnPage {
    
    /**
     *
     * @var string
     */
    protected $testUrlPage;

    /**
     *
     * @var array[array];
     */
    protected $xpath = [];

    /**
     *
     * @var boolean
     */
    protected $newShow = true;

    /**
     * 
     * @public
     * @param string $url Address url to test page
     */
    public function __construct($url) {
        $oldUrl = get_option('seo-summary-find-on-page', '');
        
        if ($url == $oldUrl) {
            $this->xpath = get_option('seo-summary-find-on-page_style', []);
            $this->newShow = false;
        }
        
        $this->testUrlPage = $url;  
        
        
    }
    
    /**
     * 
     * @public
     * @param string $xpath  DOMXpath to document element
     * @param string $bgColor Color in Hex Backgorund
     */
    public function addXpathToDistinction($xpath, $bgColor = 'red') {
        $this->xpath[] = [
                            'xpath' =>$xpath, 
                            'bgColor' => $bgColor
                        ];
    }
    
    /**
     * Init
     * 
     * @public
     */
    public function init() {
        if ($this->newShow) {
            add_option('seo-summary-find-on-page', $this->testUrlPage);
            add_option('seo-summary-find-on-page_style',$this->xpath);
            add_option('seo-summary-find-on-page-client', self::getClientIp());
        } else {
            //update_option('seo-summary-find-on-page', $this->testUrlPage);
            update_option('seo-summary-find-on-page_style',$this->xpath);
        }
        
    }
    
    /**
     * 
     * @public
     * @static
     */
    public static function printCss() {
        
        $url = get_option('seo-summary-find-on-page', 'false');

        if ($url === 'false') {  
            return;
        }
        // Only print CSS if this is a stylesheet request
        if( ! isset( $_GET['seo_summary'] ) || intval( $_GET['seo_summary'] ) !== 1 ) {
            return;
	}

  
        if ( self::getClientIp() != get_option('seo-summary-find-on-page-client')) {
            return;
        }
        
        ob_start();
	header( 'Content-type: text/css' );
        echo "/* Test page autogenerate SEO Summary */ \n";
        
        
        $css =  (get_option('seo-summary-find-on-page_style'));
        
        
        if (!is_array($css)) {
            exit;
        }
        delete_option('seo-summary-find-on-page');
        delete_option('seo-summary-find-on-page_style');
        delete_option('seo-summary-find-on-page-client');
        
        foreach ($css as $styl) {
            echo "\n";
            echo $styl['xpath'].' {';
            echo "\n";
            echo "\tbackground-color: ".$styl['bgColor'].";";
            echo "\npadding: 10px;";
            echo "\n}";
        }
        exit;
    }
    
    /**
     * Get ip client
     * 
     * @public
     * @static
     * @return string
     */
    public static function getClientIp() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}
