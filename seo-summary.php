<?php
/*
 * Plugin Name: SEO Summary
 * Version: 1.2.2
 * Description: Wtyczka kontrolująca ilość linków na stronie/poście i pokazująca ich aktualny wygląd w wyszukiwarce Google
 * Author: Klaudia Wasilewska & Piotr Kuźnik
 * Author URI: http://edokumenty.eu/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


/**
 * @var string
 */
define('PLUGIN_SEO_DIR', plugin_dir_path(__FILE__));
define('SEO_VERSION', '1.2.2');

require_once PLUGIN_SEO_DIR.'/class/CrawlPages.php';
require_once PLUGIN_SEO_DIR.'/class/SEOSummaryLinks.php';
require_once PLUGIN_SEO_DIR.'/class/SEOSummaryLinksContent.php';
require_once PLUGIN_SEO_DIR.'/class/SEOSummaryLinksManager.php';
require_once PLUGIN_SEO_DIR.'/class/DisplayLinkOnPage.php';
require_once PLUGIN_SEO_DIR.'/wp_ajax.php';

/**
 * Hook activation plugin
 */
register_activation_hook(__FILE__, function() {
    $seo = new SEOSummaryLinksManager();
    $seo->install();
    $seo->update();
});

/**
 * Hook deactivation plugin
 */
register_deactivation_hook(__FILE__, function() {
    $seo = new SEOSummaryLinksManager();
    $seo->uninstall();
});


add_action( 'wp_enqueue_scripts', function() {
    $url = home_url();

    if ( is_ssl() ) {
            $url = home_url( '/', 'https' );
    }

    if (isset($_GET['seo_summary'])) {
        wp_register_style( 'seo-summary-find-on-page', add_query_arg( array( 'seo_summary' => 1 ), $url ) );
        wp_enqueue_style( 'seo-summary-find-on-page' );
    }
}, 99 );


/**
 * Hook update plugin
 */
add_action('plugins_loaded', function() {
    DisplayLinkOnPage::printCss();
    
    
    $seo = new SEOSummaryLinksManager();
    $seo->update();
});



/**
 * Self-update hook
 */
require PLUGIN_SEO_DIR.'/update-core/plugin-update-checker.php';
$className = PucFactory::getLatestClassVersion('PucGitHubChecker');
$myUpdateChecker = new $className(
    'https://github.com/eDokumenty/seo-summary/',
    __FILE__,
    'master'
);
/**
 * end hook
 */

/*
 * Add css
 */
function add_css (){
    wp_register_style('seo-summary', plugins_url('/style.css', __FILE__));
    wp_enqueue_style('seo-summary');
}

function queue_my_admin_scripts() {
     //ustalamy odpowiedni protokół  
    if ( isset($_SERVER['HTTPS']) )  
       $protocol = 'https://';  
    else  
       $protocol = 'http://';  
  
   //pobieramy adres do pliku admin-ajax.php  
   $admin_ajax_url = admin_url( 'admin-ajax.php', $protocol );  
  
 
   //za pomocą tej funkcji przekazujemy zmienną zawierająca adres, do javascript  
    wp_enqueue_script (  'my-spiffy-miodal' ,       // handle
                        plugins_url('/js/script.js', __FILE__),  // source
                        array('jquery-ui-dialog')); // dependencies
    // A style available in WP               
    wp_enqueue_style (  'wp-jquery-ui-dialog');
}
add_action('init', 'add_css');
add_action( 'admin_enqueue_scripts', 'queue_my_admin_scripts');
wp_enqueue_script( 'tablesorter', plugins_url('/js/jquery.tablesorter.js', __FILE__), array( 'jquery' ) );

/*
 * Add plugin to the Wordpress menu
 */
function seo_summary_setup_menu(){
   

    $image = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2ZXJzaW9uPSIxLjAiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgNTAgNTAiIGZpbGw9IiNhMGE1YWEiID4gICAgPHBhdGggc3R5bGU9InRleHQtaW5kZW50OjA7dGV4dC1hbGlnbjpzdGFydDtsaW5lLWhlaWdodDpub3JtYWw7dGV4dC10cmFuc2Zvcm06bm9uZTtibG9jay1wcm9ncmVzc2lvbjp0YjstaW5rc2NhcGUtZm9udC1zcGVjaWZpY2F0aW9uOlNhbnMiIGQ9Ik0gMiAxMSBMIDIgMTMgTCAyIDM3IEwgMiAzOSBMIDQgMzkgTCA5IDM5IEwgOSAzNSBMIDYgMzUgTCA2IDE1IEwgOSAxNSBMIDkgMTEgTCA0IDExIEwgMiAxMSB6IE0gNDEgMTEgTCA0MSAxNSBMIDQ0IDE1IEwgNDQgMzUgTCA0MSAzNSBMIDQxIDM5IEwgNDYgMzkgTCA0OCAzOSBMIDQ4IDM3IEwgNDggMTMgTCA0OCAxMSBMIDQ2IDExIEwgNDEgMTEgeiBNIDExIDE3IEwgMTEgMjEgTCAzOSAyMSBMIDM5IDE3IEwgMTEgMTcgeiBNIDExIDIzIEwgMTEgMjcgTCAzNSAyNyBMIDM1IDIzIEwgMTEgMjMgeiBNIDExIDI5IEwgMTEgMzMgTCAzOSAzMyBMIDM5IDI5IEwgMTEgMjkgeiIgY29sb3I9IiMwMDAiIG92ZXJmbG93PSJ2aXNpYmxlIiBmb250LWZhbWlseT0iU2FucyI+PC9wYXRoPjwvc3ZnPg==';
    add_menu_page( 'SEO Summary', 'SEO Summary', 'manage_options', 'seo-summary', 'seo_init', $image );

    //Init cralw pages
    add_submenu_page( 'seo-summary', 'Crawl pages', 'Crawl pages',  'manage_options', 'crawl-pages', function() {
        ?>
        <div id="seo-summary">
        <?php
        $url = get_bloginfo('url').'/sitemap_index.xml';
        
        if( @file_get_contents($url) == true && home_url() !== 'http://localhost/ed' ){
            $url = get_bloginfo('url').'/sitemap_index.xml';
        } else {
            $url = 'http://edokumenty.eu/sitemap_index.xml';
        }
        
        if (isset($_GET['crawl']) && $_GET['crawl'] == 'true')  {
            libxml_use_internal_errors(true);
            $content = file_get_contents($url);
            $xml = simplexml_load_string($content);
            $crawl = new CrawlPages($xml);
            
            $seoManager = new SEOSummaryLinksManager();
            $crawl->loadManager($seoManager);
            $crawl->crawl();
        }else {  
            include PLUGIN_SEO_DIR.'templates/crawl-form.php';
        } 
        ?>
        </div>
        <?php
    } );
}
add_action('admin_menu', 'seo_summary_setup_menu');

/*
 * This function counts row in the table
 */
function numbers_of_items($ile){
    $string = "$ile";
    $ostatnia = $string[strlen($string)-1];
    
    echo '<div class="number-items tablenav">';
    if($ile == 1){
        echo $ile . ' element';
    }else if($ostatnia >1 && $ostatnia <5){
        echo $ile . ' elementy';
    }else{
        echo $ile . ' elementów';
    }
    /*
     * It writes a headlines in the table
     */
}
function write_headlines ($data){
    $i = 0;
    foreach( $data as $row ){
        foreach ( $row as $k => $v ){
            if( $i < 1 ){
                if ( $k == 'post_title' ){
                    echo '<th style="width:70%" class="seo_table_th"> SEO </th>';
                }
                if ( $k == 'post_type' ){
                    echo '<th class="seo_table_th" style="width:8%; padding-right: 50px;"> Typ postu </th>';
                    echo '<th class="th-width seo_table_th"> Ilość słów </th>';
                    echo '<th class="th-width seo_table_th" class="center"> Ilość linków<br> na stronie </th>';
                    echo '<th class="th-width seo_table_th" class="center"> Ilość linków<br> do tej strony </th>';
                }
            }
        }
        $i++;
    }
}

/*
 * Main function
 */
function seo_init(){ ?>
    <div id="seo_summary">
        <h1 class="plugin_title">SEO Summary</h1>
        <form name="formularz" method="get">        
            <?php
                $query = "SELECT p.post_title, p.post_name, p.ID, p.post_type, m.meta_key, m.meta_value FROM wp_posts AS p left join wp_postmeta AS m on ( p.ID = m.post_id and m.meta_key IN ('_yoast_wpseo_metadesc', '_yoast_wpseo_title')) WHERE p.post_type in ('post', 'page', 'rozwiazania', 'klienci', 'faq') AND p.post_status = 'publish' ORDER BY p.post_type ASC, p.post_title ASC";
                global $wpdb;
                $data = $wpdb->get_results($query);
            ?>
            <table id="sortTable" class="wp-list-table widefat fixed striped posts seo-table">
                <thead id="thead" class="static">
                    <?php write_headlines ($data); ?>
                </thead>
                <tbody>
                    <?php
                        /*
                         * It writes row in the table
                         */
                        $seoManager = new SEOSummaryLinksManager();
                        $title = '';
                        $a = [];
                        $r = [];
                        foreach( $data as $row ) {
                            if ( $title != $row->post_title ){
                                if ( !empty($title) ){
                                    $a[] = $r;
                                }
                                $r = [];
                                $title = $row->post_title;
                                $r['post_title'] = $title;
                                $url = home_url() . '/' . $row->post_name;
                                $r['url'] = $url;
                                $r['ID'] = $row->ID;
                                $r['post_type'] = $row->post_type;
                                $r['post_name'] = $row->post_name;
                            }
                            $r[$row->meta_key] = $row->meta_value;
                        }
                        $a[] = $r;
                        $ile = 0;
                        foreach ($a as $row){
                            echo '<tr>'
                                . '<td class="google-link">';
                                if ( array_key_exists('_yoast_wpseo_title', $row) ){
                                    echo '<div class="_yoast_wpseo_title post_title"><a href="' . admin_url() .'post.php?post=' . $row['ID'] . '&action=edit">' . $row['_yoast_wpseo_title'] . '</a></div>';
                                    $link_title = $row['_yoast_wpseo_title'];
                                } else {
                                    echo '<div class="post_title"><a href="' . admin_url() .'post.php?post=' . $row['ID'] . '&action=edit">' . $row['post_title'] . '</a></div>';
                                    $link_title = $row['post_title'];
                                }
                                if ($row['post_type'] !== 'faq'){
                                    echo '<div class="url"><a href="' . $row['url'] . '" target="_blank">' . $row['url'] . '</a></div>';
                                }
                                echo '<div class="_yoast_wpseo_metadesc">' . $row['_yoast_wpseo_metadesc'] . '</div>';

                                echo '</td>'
                                . '<td>' . $row['post_type'] . '</td>'
                                . '<td id="'.$link_title.'" wp_type="text" wp_value="'.$row['post_name'].'" class="open-modal right">'.$seoManager->getCountWords($row['post_name']).'</td>'
                                . '<td id="'.$link_title.'" wp_type="inLink" wp_value="'.$row['post_name'].'" class="open-modal right">'.$seoManager->countAllLinkOnPage($row['post_name']).'</td>'
                                . '<td id="'.$link_title.'" wp_type="outLink" wp_value="'.$row['post_name'].'" class="open-modal right">'.$seoManager->countAllLinkCallToPage($row['post_name']).'</td>';
                            echo '</tr>';
                            $ile++;
                        }
                        numbers_of_items($ile); ?>
                </tbody>
                <tfoot>
                    <?php write_headlines ($data); ?>
                </tfoot>
            </table>
            <?php numbers_of_items($ile); ?>
            <div id="modal-content">
                <h1 id="title_dialog"></h1>
                <div id="action_dialog"></div>
            </div>
        </form>
    </div>
<?php } ?>
