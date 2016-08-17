<?php

/**
 * Description of CrawlPages
 * 
 * @package SEO Summary
 * @since 
 * @version 0.1
 * @author Piotr Kuźnik <piotr.damian.kuznik@gmail.com>
 * @license default
 * @copyright (c) eDokumenty Sp. z o.o
 */
class CrawlPages {
    
    /**
     * Array with url map site
     * 
     * @var array[string]
     */
    private $sitemapLocation = [];
    
    /**
     *
     * @var SEOSummaryLinksManager
     */
    private $seoManager;
    
    /**
     *
     * @var integer
     */
    public $count = 0;
    
    /**
     *
     * @var array[string]
     */
    public $errors = [];
    
    /**
     *
     * @var integer
     */
    public $page = 0;
    
    /**
     *
     * @var integer
     */
    public $urls = 0;
    
    /**
     * 
     * @public
     * @construct
     * @param SimpleXMLElement $objectXML
     */
    public function __construct(SimpleXMLElement $objectXML) {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        foreach ($objectXML->sitemap as $sitemap) {
            if (!in_array((string)$sitemap->loc, $this->sitemapLocation)) {
                $this->sitemapLocation[] = (string)$sitemap->loc;
            }
        }
        
    }
    
    /**
     * 
     * @param SEOSummaryLinksManager $seoManager
     */
    public function loadManager(SEOSummaryLinksManager $seoManager) {
        $this->seoManager = $seoManager;
    }
    
    public function crawl($clearOld = true) {
        if ($clearOld) {
            $this->seoManager->truncate();
        }
        echo "<code>START</code>";
        foreach ($this->sitemapLocation as $url) {
            $object = $this->getContainsSiteMapXML($url);
                       
            foreach ($object->url as $pageUrl) {
                $pageUrl = $pageUrl->loc;
                $contentUrl = $this->crawlSimplePage($pageUrl);
                
                if (empty($contentUrl)) {
                    continue;
                }
                foreach ($contentUrl as $simple) {
                    
                    $seo = new SEOSummaryLinks();
                    $seo->setSitemap($url)
                            ->setUrl($pageUrl)
                            ->setContent_url($simple);
                    
                    
                    $this->seoManager->persist($seo);
                }
            }
        }
        ?>
        <code>END.</code>
        </br>
        </br>
        <p>Przeczytano : <?php echo $this->page; ?> stron/y</p>
        </br>
        <p>Znaleziono w nich : <?php echo $this->urls; ?> adresów URL.</p>
        </br>
        <p>Błędów( <?php echo count($this->errors); ?> ) :</p>
        <ul>
            <?php
                    foreach ($this->errors as $str) {
                        echo '<li>'.$str.'</li>';
                    }
            
            ?>
        </ul>
        <?php
        //$this->seoManager->flush();
    }
    
    /**
     * 
     * @param string $sitemapUrl
     * @return SimpleXMLElement
     */
    protected function getContainsSiteMapXML($sitemapUrl) {
        libxml_use_internal_errors(true);
        $content = file_get_contents($sitemapUrl);
        return simplexml_load_string($content);
    }
    
    /**
     * 
     * @param string $pageUrl
     * @return array
     */
    public function crawlSimplePage($pageUrl) {
        $this->count++;
        $url = [];
        echo "<code>&nbsp;";
        $handle = curl_init($pageUrl);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($handle);
        
        if (curl_errno($handle)) {
             $this->errors[] = "{$this->count}. $pageUrl --- #Błąd : ".curl_errno($handle).' : '.curl_error($handle);
             
        } else {
            $this->page++;
            if (!empty($html)) {
                $doc = new DOMDocument();
                $doc->loadHTML($html);

                $elements = $doc->getElementsByTagName('a');
                if (!is_null($elements)) {
                    foreach ($elements as $element) {

                        foreach ($element->attributes as $attribute) {
                            if ($attribute->name != 'href') {
                                continue;
                            }
                            $url[] = $attribute->value;
                            $this->urls++;
                        }

                    }
                }
            } else {
                $this->errors[] = "{$this->count}. $pageUrl --- #Błąd : Empty output CURL";
            }
        }
        curl_close($handle);
        echo "</code>";
        return $url;
    }
}
