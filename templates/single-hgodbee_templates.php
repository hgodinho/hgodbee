<?php
/**
 *
 */
wp_head();

if (is_user_logged_in()) {
    $config         = include dirname(plugin_dir_path(__FILE__)) . '/config/config.php';
    $init_beeplugin = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-starter.php';
    $container      = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-container.php';
    $modals         = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-modals.php';
    $navbar         = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-navbar.php';
    
    //$used_terms = get_the_terms( $post->ID, $config['prefix'] . 'tax' );
    //HGodBee::hb_var_dump($used_terms, __CLASS__, __METHOD__, __LINE__, true);
    
    hgobee_navbar();
    hgodbee_beeplugin_notification_area();
    hgodbee_plugin_container();
    hgodbee_modal_template_save();
    //hgodbee_modal_send_test();

    $hgodbee_action = get_query_var('action');
    $client         = get_option($config['prefix'] . 'settings_name');
    $uid            = $client['hgodbee_uid'];
    $id             = $client['hgodbee_id'];
    $secret         = $client['hgodbee_secret'];
    $beeplugin      = new BeeFree($id, $secret);
    $token          = $beeplugin->getCredentials();
    $tokenJSON      = json_encode($token);

    if ('comece-do-zero' === $post->post_name) {
        $template = get_the_content();
        hgodbee_plugin_starter($tokenJSON, $uid, $template);
    } else {
        if ('edit' === $hgodbee_action) {
            $template = get_the_content();
            hgodbee_plugin_starter($tokenJSON, $uid, $template);
        }
        if ('view' === $hgodbee_action) {
            $html = get_post_meta(get_the_ID(), $config['prefix'] . 'saved_html');
            if (isset($html[0])) {
                hgodbee_edit_button();
                echo '<div class="container">';
                echo $html[0];
                echo '</div>';
            }
        }
    }

} else {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part(404);exit();
}
wp_footer();