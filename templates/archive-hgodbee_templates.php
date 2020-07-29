<?php
/**
 *
 */
wp_head();

if (is_user_logged_in()) {
    $config = include dirname(plugin_dir_path(__FILE__)) . '/config/config.php';
    $card   = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-card.php';
    $args   = array(
        //'post_type'      => 'template_bee',
        'post_type'      => HB_PREFIX . 'templates',
        'posts_per_page' => '10',
        'order'          => 'ASC',
        'order_by'       => 'date',
    );
    $templates = new WP_Query($args);
    if ($templates->have_posts()) {
        ?>
<div class="ui container">
    <h2 class="ui header">Selecione um template.</h2>
    <div class="ui four column grid">
        <?php
while ($templates->have_posts()) {
            $templates->the_post();
            $terms        = get_the_terms(get_the_ID(), HB_PREFIX . 'tax');
            $terms_in_use = array();
            //HGodBee::hb_var_dump($terms, __CLASS__, __METHOD__, __LINE__, false);
            if (has_term('', HB_PREFIX . 'tax')) {
                foreach ($terms as $term) {
                    array_push($terms_in_use, $term->name);
                }
                hgodbee_plugin_card($terms_in_use);
            } else {
                hgodbee_plugin_card('');
            }
        }
        ?>
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