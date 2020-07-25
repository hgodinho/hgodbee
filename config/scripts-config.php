<?php
/**
 * Configurações dos scripts a serem carregados
 */
$config = include plugin_dir_path(__FILE__) . '/config.php';

$bee_handle = $config['prefix'] . 'bee';

$scripts = array(
    'bee'       => array(
        'hook'      => 'wp_enqueue_scripts',
        'handle'    => $bee_handle,
        'src'       => 'https://app-rsrc.getbee.io/plugin/BeePlugin.js',
        'deps'      => '',
        'ver'       => HB_VERSION,
        'in_footer' => true,
    ),
    'bee_app'       => array(
        'hook'      => 'wp_enqueue_scripts',
        'handle'    => $config['prefix'] . 'bee_app',
        'src'       => dirname(plugin_dir_url(__FILE__)) . '/js/bee-app.js',
        'deps'      => array($bee_handle),
        'ver'       => HB_VERSION,
        'in_footer' => true,
    ),
    'bootstrap'       => array(
        'hook'      => 'wp_enqueue_scripts',
        'handle'    => $config['prefix'] . 'bootstrap_js',
        'src'       => dirname(plugin_dir_url(__FILE__)) . '/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js',
        'deps'      => array('jquery'),
        'ver'       => HB_VERSION,
        'in_footer' => true,
    ),
    'admin_bootstrap' => array(
        'hook'      => 'admin_enqueue_scripts',
        'handle'    => $config['prefix'] . 'bootstrap_admin_js',
        'src'       => dirname(plugin_dir_url(__FILE__)) . '/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js',
        'deps'      => array('jquery'),
        'ver'       => HB_VERSION,
        'in_footer' => true,
    ),
    'admin_ajax'      => array(
        'hook'      => 'load-toplevel_page_hgodbee_menu',
        'handle'    => $config['prefix'] . 'admin_ajax',
        'src'       => dirname(plugin_dir_url(__FILE__)) . '/js/ajax-admin.js',
        'deps'      => array('jquery'),
        'ver'       => HB_VERSION,
        'in_footer' => true,
    ),
);

return $scripts;