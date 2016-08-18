<?php
/*
 * Plugin Name: SEO Summary
 * Author: Klaudia Wasilewska & Piotr Kuźnik
 * Description: Wtyczka kontrolująca ilość linków na stronie/poście i pokazująca aktualny wygląd w wyszukiwarce Google
 * Version: 1.0
 */

/**
 * @var string
 */
define('PLUGIN_SEO_DIR', plugin_dir_path(__FILE__));

require_once PLUGIN_SEO_DIR.'/class/CrawlPages.php';
require_once PLUGIN_SEO_DIR.'/class/SEOSummaryLinks.php';
require_once PLUGIN_SEO_DIR.'/class/SEOSummaryLinksManager.php';


/**
 * Hook activation plugin
 */
register_activation_hook(__FILE__, function() {
    $seo = new SEOSummaryLinksManager();
    $seo->install();
});

/**
 * Hook deactivation plugin
 */
register_deactivation_hook(__FILE__, function() {
    $seo = new SEOSummaryLinksManager();
    $seo->uninstall();
});


require PLUGIN_SEO_DIR.'/update-core/plugin-update-checker.php';
$className = PucFactory::getLatestClassVersion('PucGitHubChecker');
$myUpdateChecker = new $className(
    'https://github.com/eDokumenty/seo-summary/',
    __FILE__,
    'master'
);


/*
 * Add css
 */
function add_css (){
    wp_register_style('seo-summary', plugins_url('/style.css', __FILE__));
    wp_enqueue_style('seo-summary');
}
add_action('init', 'add_css');
/*
 * Add JS
 */
function add_js() {
    wp_register_script('seo', plugins_url('/script.js', __FILE__), array('jquery'));
    wp_register_script('easing', plugins_url('/script.js', __FILE__), array('jquery'));
    wp_enqueue_script('seo');
    wp_enqueue_script('easing');
}
add_action('init', 'add_js');

/*
 * Add plugin to the Wordpress menu
 */
function seo_summary_setup_menu(){
    add_menu_page( 'SEO Summary', 'SEO Summary', 'manage_options', 'seo-summary', 'seo_init' );

    //Init cralw pages
    add_submenu_page( 'seo-summary', 'Crawl pages', 'Crawl pages',  'manage_options', 'crawl-pages', function() {
        ?>
        <div id="seo-summary">
        <?php
        $url = get_bloginfo('url').'/sitemap_index.xml';
        $url = 'http://edokumenty.eu/sitemap_index.xml';
        if (isset($_GET['crawl']) && $_GET['crawl'] == 'true')  {
            libxml_use_internal_errors(true);
            $content = file_get_contents($url);
            $xml = simplexml_load_string($content);
            $crawl = new CrawlPages($xml);
            
            $seoManager = new SEOSummaryLinksManager();
            $crawl->loadManager($seoManager);
            $crawl->crawl();
            
        }else {
            
            libxml_use_internal_errors(true);
            $content = file_get_contents($url);
            $xml = simplexml_load_string($content);
            if (!$xml) {
                echo '<b>Failed loading XML with url:</b> ' . $url;
                return;
            }
                      
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
    
    echo '<div class="number-items tablenav number_record">';
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
    echo '<tr>';
    /*  <td class="checkbox-col">
     *      <input id="all" class="checkbox-td all" type="checkbox" onclick="Zaznacz()">
     *  </td>
     */
    $i = 0;
    foreach( $data as $row ){
        foreach ( $row as $k => $v ){
            if( $i < 1 ){
                if ( $k == 'post_title' ){
                    echo '<th class="seo_table_th"> SEO </th>';
                }
                if ( $k == 'post_type' ){
                    echo '<th class="seo_table_th"> Typ postu </th>';
                }
            }
        }
        echo '</tr>';
        $i++;
    }
}
/*
 * Main function
 */
function seo_init(){ ?>
    <div id="seo_summary">
        <h1 class="plugin_title">SEO Summary</h1>
        <form class="form-plugin" name="formularz" method="get">        
            <?php
                $query = "SELECT p.post_title, p.post_name, p.ID, p.post_type, m.meta_key, m.meta_value FROM wp_posts AS p left join wp_postmeta AS m on ( p.ID = m.post_id and m.meta_key IN ('_yoast_wpseo_metadesc', '_yoast_wpseo_title')) WHERE p.post_type in ('post', 'page', 'rozwiazania', 'klienci', 'faq') AND p.post_status = 'publish' ORDER BY p.post_type ASC, p.post_title ASC";
                global $wpdb;
                $data = $wpdb->get_results($query);
            ?>
            <table class="wp-list-table widefat fixed striped posts seo-table">
                <thead>
                    <?php write_headlines ($data); ?>
                </thead>
                <tbody>
                    <?php
                        /*
                         * It writes row in the table
                         */
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
                            }
                            $r[$row->meta_key] = $row->meta_value;
                        }
                        $a[] = $r;
                        $ile = 0;
                        foreach ($a as $row){
                            
                            /*
                             * <td><input id="p' . $ile . '" type="checkbox"></td>
                             */
                            
                            echo '<tr>'
                                . '<td class="google-link">';
                                if ( array_key_exists('_yoast_wpseo_title', $row) ){
                                    echo '<div class="_yoast_wpseo_title post_title"><a href="' . admin_url() .'post.php?post=' . $row['ID'] . '&action=edit">' . $row['_yoast_wpseo_title'] . '</a></div>';
                                } else {
                                    echo '<div class="post_title"><a href="' . admin_url() .'post.php?post=' . $row['ID'] . '&action=edit">' . $row['post_title'] . '</a></div>';

                                }
                                if ($row['post_type'] !== 'faq'){
                                    echo '<div class="url"><a href="' . $row['url'] . '" target="_blank">' . $row['url'] . '</a></div>';
                                }
                                echo '<div class="_yoast_wpseo_metadesc">' . $row['_yoast_wpseo_metadesc'] . '</div>';

                                echo '</td>'
                                . '<td>' . $row['post_type'] . '</td>'
                            . '</tr>';
                            $ile++;
                        }
                        numbers_of_items($ile); ?>
                </tbody>
                <tfoot>
                    <?php write_headlines ($data); ?>
                </tfoot>
            </table>
            <?php numbers_of_items($ile); ?>
        </form>
    </div>
<?php } ?>