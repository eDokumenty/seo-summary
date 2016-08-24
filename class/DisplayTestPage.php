<?php

/**
 * Description of DisplayTestPage
 * 
 * @package SEO Summary
 * @since 
 * @version 0.1
 * @author Piotr Kuźnik <piotr.damian.kuznik@gmail.com>
 * @license gpl
 * @copyright (c) eDokumenty Sp. z o.o
 */
class DisplayTestPage {
    
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
     * @public
     * @param string $url Address url to test page
     */
    public function __construct($url) {
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
        add_option(__CLASS__, $url);
        add_option(__CLASS__.'_style', serialize($this->xpath));
    }
    
    /**
     * 
     * @public
     * @static
     */
    public static function printCss() {
        ob_start();
        
        $url = get_option(__CLASS__, false);
        
        if ($url === FALSE || $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] !== $url ) {
            return;
        }
        echo '<style type="text/css">';
        echo "/* Test page autogenerate SEO Summary */ \n";
        
        $css = @unserialize(get_option(__CLASS__.'_style'));

        if (!is_array($css)) {
            echo '</style>';
            return;
        }
        delete_option(__CLASS__);
        delete_option(__CLASS__.'_style');
        
       
        
        foreach ($css as $styl) {
            echo "\n";
            echo $styl['xpath'].' {';
            echo "\n";
            echo "\tbackground-color: ".$styl['bgColor'];
            echo "\n}";
        }
        echo '</style>';
    }
}
