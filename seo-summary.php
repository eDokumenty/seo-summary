<?php
/*
 * Plugin Name: SEO Summary
 * Author: Klaudia Wasilewska
 * Description: Wtyczka kontrolująca ilość linków na stronie/poście
 * Version: 0.1
 */


require_once __DIR__.'/class/CralwPages.php';

/**
 * Hook activation plugin
 */
register_activation_hook(__FILE__, function() {
    $seo = new CralwPages();
    $seo->install();
});

/**
 * Hook deactivation plugin
 */
register_deactivation_hook(__FILE__, function() {
    $seo = new CralwPages();
    $seo->uninstall();
});


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
    add_submenu_page( 'seo-summary', 'Cralw pages', 'Cralw pages',  'manage_options', 'cralw-pages', function() {

        $url = get_bloginfo('url').'/sitemap_index.xml';
        
        try{
            $xml = simplexml_load_string(file_get_contents($url));
        }
        catch(Exception $e) {
            echo 'Nie znaleziono strony: '.$url;
            return;
        }
        ?>
        <div id="seo-summary">
            <form method="GET">
                <table class="wp-list-table widefat fixed striped posts seo-table">
                    <thead>
                        <tr>
                            <td class="checkbox-col">
                                <input id="all" class="checkbox-td all" type="checkbox" onclick=Zaznacz()>
                            </td>
                            <td>
                                Nazwa mapy strony
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php



                        ?>
                    </tbody>
                    <tfoot>
                         <tr>
                            <td class="checkbox-col">
                                <input id="all" class="checkbox-td all" type="checkbox" onclick=Zaznacz()>
                            </td>
                            <td>
                                Nazwa mapy strony
                            </td>
                        </tr>
                    </tfoot>
            </form>
        </div>
        <?php
    } );
}
add_action('admin_menu', 'seo_summary_setup_menu');

/*
 * This function counts all articles in the table
 */
function numbers_of_items($ile){
    echo '<div class="number-items tablenav number_record">';
    if($ile == 1){
        echo $ile . ' element';
    }else if($ile >1 && $ile <5){
        echo $ile . ' elementy';
    }else{
        echo $ile . ' elementów';
    }
    echo '</div>';
}
/*
 * It writes a headlines in the table
 */
function write_headlines ($data){
    echo '<tr>
    <td class="checkbox-col">
        <input id="all" class="checkbox-td all" type="checkbox" onclick=Zaznacz()>
    </td>';
    $i =0;
    foreach( $data as $row ){
        foreach ( $row as $k => $v ){
            if( $i < 1 ){
                if ($k == 'post_title'){
                    echo '<th class="seo_table_th"> SEO </th>';
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
        <form class="form-plugin" name="formularz" method="get">        
            <?php
            $query = "SELECT p.post_title, p.post_name, p.ID, m.meta_key, m.meta_value FROM wp_posts AS p left join wp_postmeta AS m on ( p.ID = m.post_id and m.meta_key ='_yoast_wpseo_metadesc') WHERE p.post_type in ('post', 'page', 'rozwiazania', 'klienci') AND p.post_status = 'publish' ORDER BY `p`.`post_name` ASC";
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
                            }
                            $r[$row->meta_key] = $row->meta_value;
                        }
                        $a[] = $r;
                        $ile = 0;
                        foreach ($a as $row){
                            echo '<tr><td><input id="p' . $ile . '" type="checkbox"></td><td class="google-link">';
                            foreach ($row as $k => $v){
                                if( !empty($k) ){
                                    if ($k == 'post_title'){
                                        $v = '<a href="' . admin_url() .'post.php?post=' . $row['ID'] . '&action=edit">' . $row['post_title'] . '</a>';
                                    }
                                    if ($k != 'ID'){
                                        echo '<div class="'. $k .'">' . $v . '</div>';
                                    }
                                }
                            }
                            echo '</td></tr>';
                            $ile++;
                        }
                        numbers_of_items($ile);
                        ?>
                </tbody>
                <tfoot>
                    <?php write_headlines ($data); ?>
                </tfoot>
            </table>
            <?php numbers_of_items($ile); ?>
        </form>
    </div>
<?php } ?>