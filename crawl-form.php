<?php
libxml_use_internal_errors(true);
$content = file_get_contents($url);
$xml = simplexml_load_string($content);
if (!$xml) {
    echo '<b>Failed loading XML with url:</b> ' . $url;
   // return;
}

?>
<div id="seo-summary">
    <form action="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=crawl-pages&crawl=true" method="post" class="form-plugin" name="formularz" action>
        <input type="submit" value="Indeksuj" />
        <table class="wp-list-table widefat fixed striped posts seo-table">
            <thead>
                <tr>
                    <td class="checkbox-col">
                        <input id="all" class="checkbox-td all" type="checkbox" onclick=Zaznacz()>
                    </td>
                    <td>Nazwa mapy strony</td>
                    <td>Ostatnia modyfikacja</td>
                </tr>
            </thead>
            <tbody>
                <?php
                        $ile = 0;
                        foreach ($xml->sitemap as $sitemap) {
                            echo '<tr>';
                            echo '<td><input id="p' . $ile . '"  type="checkbox" name="sitemap[]" value="'.$sitemap->loc.'"></td>';
                            echo '<td>'.$sitemap->loc.'</td>';
                            echo '<td>'.$sitemap->lastmod.'</td>';
                            echo '</tr>';
                            $ile++;
                        }


                ?>
            </tbody>
            <tfoot>
                 <tr>
                    <td class="checkbox-col">
                        <input id="all" class="checkbox-td all" type="checkbox" onclick=Zaznacz()>
                    </td>
                    <td>Nazwa mapy strony</td>
                    <td>Ostatnia modyfikacja</td>
                </tr>
            </tfoot>
    </form>
    <input type="submit" onclick="Zaznacz()" value="Indeksuj wszystko" />
</div>
<?php