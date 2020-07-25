<?php
/**
 *
 */
wp_head();

if (is_user_logged_in()) {
    $cpt  = include dirname(plugin_dir_path(__FILE__)) . '/config/cpt-config.php';
    $args = array(
        //'post_type'      => 'template_bee',
        'post_type'      => $cpt['name'],
        'posts_per_page' => '10',
        'order'          => 'ASC',
        'order_by'       => 'date',
    );
    $templates = new WP_Query($args);
    if ($templates->have_posts()) {
        ?>
<div class="container">
    <h2 class="py-4">Selecione um template.</h2>
    <div class="card-columns">
        <?php
while ($templates->have_posts()) {
            $templates->the_post();
            ?>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"><?php the_title();?></h4>
                <p class="card-text"><?php the_excerpt();?></p>
                <a href="<?php the_permalink();?>" class="btn btn-primary">Selecionar</a>
            </div>
        </div>
        <?php
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