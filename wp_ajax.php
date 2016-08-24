<?php
/**
 * Hook ajax test page
 * 
 * @public 
 * @function wp_ajax_test_page
 */
add_action('wp_ajax_test_page', function () {
    $url = $_POST['url'];
    $xpath = $_POST['xpath'];
    $bgColor = $_POST['bgColor'];
    
    
    $test = new DisplayTestPage($url);
    $test->addXpathToDistinction($xpath, $bgColor);
    
    
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

    if( @file_get_contents($url) == true && home_url() !== 'http://localhost/ed' ){
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
            . '</thead>';
        foreach ($data as $row){
            echo '<tr>';
            echo '<td class="td-lp" style="width:50px;text-align: center;">'.$lp++.'</td>';
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