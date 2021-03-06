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
     * @var integer
     */
    public $words = 0;
    
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
        $startTime = $this->getMicrotime();
        if ($clearOld) {
            $this->seoManager->truncate();
        }
        foreach ($this->sitemapLocation as $url) {
            $object = $this->getContainsSiteMapXML($url);
                       
            foreach ($object->url as $pageUrl) {
                $pageUrl = $pageUrl->loc;
                $data = $this->crawlSimplePage($pageUrl);
                $contentUrl = $data['url'];
                
                if (empty($contentUrl)) {
                    continue;
                }

                $seo = new SEOSummaryLinks(null,
                                            $url, 
                                            $pageUrl,
                                            $data['cwords']);
                $seo->setContent($contentUrl);
                $this->seoManager->persist($seo);
            }
        }
        
        ?>
        </br>
        </br>
        <p>Przeczytano : <?php echo $this->page; ?> stron/y</p>
        <p>Znaleziono w nich : <?php echo $this->urls; ?> adresów URL.</p>
        <p>Słów : <?php echo $this->words; ?></p>
        <p>Błędów( <?php echo count($this->errors); ?> ) :</p>
        <ul>
            <?php
                    foreach ($this->errors as $str) {
                        echo '<li>'.$str.'</li>';
                    }
            
            ?>
        </ul>
        <?php
        $this->seoManager->flush();
        echo "</br></br>Dane zostały zapisane..";
        
        $endTime = $this->getMicrotime();
        $timeExecute = $endTime - $startTime;
        
        $minuty = floor($timeExecute/60);
        $sekundy = floor($timeExecute - $minuty*60);
        
        echo '<p>Operacje wykonano w czasie: '.$timeExecute.' sekund tj. ok.: '. $minuty .' minut '. $sekundy .' sekund</p>';
    }
    
    /**
     * 
     * @protected
     * @return float
     */
    protected function getMicrotime() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
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
        echo "<code>";
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

                $countWords = $this->countWords(self::html2string($html,['a']));
                
                $this->words += $countWords;
                
                $elements = $doc->getElementsByTagName('a');
                if (!is_null($elements)) {
                    foreach ($elements as $element) {

                        foreach ($element->attributes as $attribute) {
                            if ($attribute->name != 'href') {
                                continue;
                            }
                            
                            //Remove empty href
                            if (empty($attribute->value)) {
                                continue;
                            }
                            
                            $home_url = (home_url() == 'http://localhost/ed') ? 'http://edokumenty.eu' : ((empty(home_url())? 'http://edokumenty.eu' : home_url() ));
                            $path_url = rtrim($attribute->value, "/");
                            $pattern = '/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/';
                            if (!preg_match($pattern, $path_url)) {
                               continue;
                            }
                            
                            $xpath = $element->getNodePath();
                            
                            $seo = new SEOSummaryLinksContent(null,
                                                                null,
                                                                $path_url,
                                                                $xpath);
                           
                            $url[] = $seo;
                            
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
        return [ 
            'url' => $url,
            'cwords' => $countWords,
                ];
    }
    
    /**
     * 
     * @static
     * @public
     * @param string $html
     * @param array $removeTags
     * @return string
     */
    public static function html2string($html, $removeTags = []) {
        $text = '';

        
        
        // Extract body section if exists
        preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $matches);
        $text = $matches ? $matches[1] : $html;
        // Cut style sections
        $text = preg_replace('/[\n\r]*<style[^>]*>.*?<\/style>[\n\r]*/is', '', $text);
        // Cut high html emtities >= &#1000;  (fast, temporary solution)
        $text = preg_replace('/\&\#[0-9]{4,}\;/', '?', $text);

        //cut scripts
        $text = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $text); 
        
        //cut tags
        foreach ($removeTags as $tag) {
            $text = preg_replace('/<'.$tag.'\b[^>]*>(.*?)<\/'.$tag.'>/is', "", $text); 
        }
        
        
        // Enclose TABLE element with BR tags
        $text = str_ireplace(array('<table','</table>','</tr>'), array('[#br]<table', '', '[#/tr]'), $text);
        // Enclose P element with BR tags
        $text = str_ireplace(array('<p','</p>'), array('[#br]<p','[#br]'), $text);
        // Replace (closing) DIV tags with BR tag

//-- BUG ---
        //Reduce count of [#/div] tags (note: ~600 succesive tags caused silent script crash)
        $text = preg_replace('/(?:\s*<\/div>\s*){10,200}/is', '[#/div]', $text);
        $text = preg_replace('/(?:\s*<\/div>\s*){10,200}/is', '[#/div]', $text);
        $text = preg_replace('/(?:\[\#div\]){2,}/is', '[#/div]', $text);

        // Replace the EOLs contained between PRE,TEXTAREA tags by BR tags
        $text = preg_replace_callback('/<(pre|textarea)[^>]*>(.*?)<\/\1>/is', function($matches){return str_replace(["\r", "\n"], ['', '<br>'], $matches[2]);}, $text);

        // Replace all sequences of white-spaces-and-end-of-lines to simple space char
        $text = preg_replace('/\s+/', ' ', $text);

        // separate columns with TAB
        $text = preg_replace('/<\/td><td[^>]*>/i', "\t", $text);

        // Clear all of the needles EOL
        $text = str_replace(array("\r", "\n", '&nbsp;'), array('', '', ' '),$text);
        // Change all of the BR and H1..9(header) tags to NL
        $text = preg_replace('/(?:<br[^>]*>)|(?:<h\d[^>]*>)|(?:<\/h\d>)/i', '[#br]', $text);

		$text = strip_tags($text);
		$text = preg_replace('/(?:\[\#\/(tr|div)\])+/s', '[#br]', $text);

        // Strip spaces between [#br] tags
        $text = preg_replace('/(?:\s*\[\#br\]\s*)/s', '[#br]', $text);

//-- BUG ---
        // Reduce count of [#br] tags (note: ~600 succesive tags caused silent script crash)
        $text = preg_replace('/(?:\[\#br\]){4,200}/', '[#br][#br][#br]', $text);
        $text = preg_replace('/(?:\[\#br\]){4,200}/', '[#br][#br][#br]', $text);

        // Replace [#br] tags to new lines
        $text = preg_replace('/\[\#br\]/s', "\r\n", $text);

		$dd = mb_detect_encoding($text, 'ascii, utf-8, iso-8859-2, iso-8859-1');
		if (empty($dd)) $dd = 'utf-8';

        // Convert encoding for html_entity_decode if current charset not supported
        if(!in_array(strtolower($dd), array('utf-8', 'iso-8859-1'))) {
            $text = iconv($dd, 'utf-8', $text);
            $dd = 'utf-8';
        }

        // Strip tags, decode html entities and special characters
        $text = htmlspecialchars_decode(html_entity_decode($text, ENT_COMPAT, $dd));
        return $text;
    }
    
    /**
     * 
     * @param string $text
     * @return integer
     */
    public function countWords($text) {
        $text = preg_replace('/\s\s+/', ' ',$text);
        $words = explode(' ', $text);
  
        $countWords = 0;
        
        foreach ($words as $word) {
            if (empty($word)) {
                continue;
            }
            $countWords++;
        }
        return $countWords;
    }

    /**
     * 
     * @static
     * @public
     * @param string $pageUrl
     * @return string
     */
    public static function getTextSimplePage($pageUrl) {
        $handle = curl_init($pageUrl);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($handle);
        
        if (curl_errno($handle)) {
          return false;
             
        } else {
            if (!empty($html)) {
                return self::html2string($html,['a']); 
            } else {
               return null;
            }
        }
        curl_close($handle);
        return false;
    }
}
