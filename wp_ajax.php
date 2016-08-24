<?php
/**
 * Hook ajax findOnPage
 * 
 * @public 
 * @function wp_ajax_findOnPage
 */
add_action('wp_ajax_findOnPage', function () {
    $elid = intval($_POST['elid']);
    $bgColor = $_POST['bgColor'];
    
    $seo = new SEOSummaryLinksManager();
    
    $url = $seo->getUrlParent($elid);
    $xpath = $seo->getXpath($elid);
    
    
    $test = new DisplayTestPage($url);
    $test->addXpathToDistinction($xpath, $bgColor);
    
    
    $test->init();
    echo $url.'#seo-test';
});

/**
 * Hook ajax crawl
 * 
 * @public
 * @function wp_ajax_crawl_now
 */
add_action('wp_ajax_crawl_now', function (){

    $url = get_bloginfo('url').'/sitemap_index.xml';

    if( @file_get_contents($url) == true ){
        $url = get_bloginfo('url').'/sitemap_index.xml';
    } else {
        $url = 'http://edokumenty.eu/sitemap_index.xml';
    }

    libxml_use_internal_errors(true);
    $content = file_get_contents($url);
    $xml = simplexml_load_string($content);
    $crawl = new CrawlPages($xml);

    $seoManager = new SEOSummaryLinksManager();
    $crawl->loadManager($seoManager);
    $crawl->crawl();
});


add_action('wp_ajax_get_text', function() {
    $post = $_POST['post_name'];
    
    $seo = new SEOSummaryLinksManager();
    $url = $seo->getUrl($post);
    
    $text = CrawlPages::getTextSimplePage($url);
    
    ?>
<pre>
    <?php echo $text; ?>
</pre>
    <?php
    return true;
});

add_action('wp_ajax_get_inLink', function() {
   $post = $_POST['post_name']; 
   $seo = new SEOSummaryLinksManager();
   $data = $seo->getAllLinkOnPage($post);
   ?>

    <?php
        $lp = 1;
        echo '<table class="wp-list-table widefat striped posts">';
        echo '<thead>'
                . '<th style="text-align: center;">L.p</th>'
                . '<th>URL</th>'
                . '<th>Ile razy</th>'
                . '<th></th>'
            . '</thead>';
        foreach ($data as $row){
            echo '<tr>';
            echo '<td class="td-lp" style="width:50px;text-align: center;">'.$lp++.'</td>';
            echo '<td>'.$row['url'].'</td>';
            echo '<td>'.$row['replay'].'</td>';
            echo '<td><img onclick="findOnPage('.$row['ID'].');" width="16" height="16" title="Find on page" alt="icon" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAQAAADZc7J/AAAAAmJLR0QA/4ePzL8AAAAJcEhZcwAAAEgAAABIAEbJaz4AAAAJdnBBZwAAACAAAAAgAIf6nJ0AAAE3SURBVEjHpZWrdoQwEIY/eiIQESsQiBUVK1ZU9P2fArECUVGBWIFARCByDhXccpnskvKbHCYzH5lJMikmzunjZPx5gJqHYreUXLmgly/DQMcoB09AMfmAG1fBs+PnCEDxvf05lKHBvga44U96LKCoqNMIH7Au3vDwctbcF3CUyOTsQrmFN0HJDA0GgCtlnNsKWEvXxpliaQMvAXBZcjeJEj4dLxEwZ9mTUu94iYBZYxJgUxM+QJGtowD1DjAXr0oCKsdLBAwA1NJOA3o5j0Ma0C3jl7BYxT3wEgDjMqmjC7VbjLRLxy/TbG/d4JzrLCKON5TRKa6DkAByS7Pe2jaEDNhr7x5gJSFeA2JghHAbynvZrbEA1OvZyHkXQsQtFxAidD7ARwxkFXGX4hON4XeyG+D/Ov24/gEdpW215hlpNwAAACV0RVh0ZGF0ZTpjcmVhdGUAMjAxMC0wMi0xMVQxMTo1MDowOC0wNjowMNYQZfsAAAAldEVYdGRhdGU6bW9kaWZ5ADIwMDYtMDUtMDVUMTM6MjI6NDAtMDU6MDC/5P4aAAAAAElFTkSuQmCC" /> </td>';
            echo '</tr>';
        }
        echo '</table>';
        if ( $lp == 1 ){
            echo '<p>Brak wyników na stronie ...</p>';
        }
    return true;
});

add_action('wp_ajax_get_outLink', function(){
    $post = $_POST['post_name'];
    
    $seo = new SEOSummaryLinksManager();
    
    $data = $seo->getAllLinkCallToPage($post);
   ?>

<?php
        $lp = 1;
        echo '<table class="wp-list-table widefat striped posts">';
        echo '<thead>'
                . '<th style="text-align: center;">L.p</th>'
                . '<th>URL</th>'
                . '<th>Ile razy</th>'
            . '</thead>';
        foreach ($data as $row){
            echo '<tr>';
            echo '<td style="width:50px;text-align: center;">'.$lp++.'</td>';
            echo '<td>'.$row['url'].'</td>';
            echo '<td>'.$row['replay'].'</td>';
            echo '</tr>';
        }
        echo '</table>';
        if ( $lp == 1 ){
            echo '<p>Brak wyników na stronie ...</p>';
        }
    return true;
});