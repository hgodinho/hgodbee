<?php
/**
 *
 */
wp_head();

if (is_user_logged_in()) {
    $config         = include dirname(plugin_dir_path(__FILE__)) . '/config/config.php';
    $init_beeplugin = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-starter.php';
    $notification   = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-notification.php';
    $container      = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-container.php';
    $modals         = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-modals.php';

    hgodbee_beeplugin_notification_area();

    $client    = get_option($config['prefix'] . 'settings_name');
    $uid       = $client['hgodbee_uid'];
    $id        = $client['hgodbee_id'];
    $secret    = $client['hgodbee_secret'];
    $beeplugin = new BeeFree($id, $secret);
    $token     = $beeplugin->getCredentials();
    $tokenJSON = json_encode($token);

    hgodbee_plugin_container();
    hgodbee_modal_template_save();
    hgodbee_modal_send_test();

    $template = get_the_content();

    hgodbee_plugin_starter($tokenJSON, $uid, $template);

} else {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part(404);exit();
}
wp_footer();