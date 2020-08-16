<?php
/**
 *
 */
wp_head();

if (is_user_logged_in()) {
    $config = include dirname(plugin_dir_path(__FILE__)) . '/config/config.php';
    $card   = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-card.php';
    $modals = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-modals.php';
    $args   = array(
        //'post_type'      => 'template_bee',
        'post_type'      => HB_PREFIX . 'templates',
        'posts_per_page' => '10',
        'order'          => 'ASC',
        'order_by'       => 'date',
    );
    $templates = new WP_Query($args);
    if ($templates->have_posts()) {
        hgodbee_beeplugin_notification_area();
        hgodbee_modal_template_delete();
        ?>
<div class="hgodbee-container">
    <div class="ui container">
        <h2 class="ui huge center aligned inverted icon header _margin-bottom-4-100"><span class="dashicons dashicons-archive icon"></span></i>Selecione um template</h2>
        <div class="ui four column grid">
            <?php
while ($templates->have_posts()) {
            $templates->the_post();
            $taxs        = get_the_terms(get_the_ID(), HB_PREFIX . 'tax');
            $tags        = get_the_terms(get_the_ID(), HB_PREFIX . 'tag');
            $tags_in_use = array();
            $taxs_in_use = array();

            if (has_term('', HB_PREFIX . 'tax') &&  has_term('', HB_PREFIX . 'tag')) {
                foreach ($taxs as $tax) {
                    array_push($taxs_in_use, $tax->name);
                }
                foreach ($tags as $tag) {
                    array_push($tags_in_use, $tag->name);
                }
                hgodbee_plugin_card($taxs_in_use, $tags_in_use);
            } else {
                hgodbee_plugin_card('', '');
            }
        }
        ?>
        </div>
    </div>
</div>
<?php
}
} else {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part(404);exit();
}

wp_footer();