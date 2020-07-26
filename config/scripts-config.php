<?php
/**
 * Configurações dos scripts a serem carregados
 */
$config = include plugin_dir_path(__FILE__) . '/config.php';

$bee_handle = $config['prefix'] . 'bee';

$scripts = array(
    'semantic_ui'       => array(
        'hook'      => 'wp_enqueue_scripts',
        'handle'    => $config['prefix'] . 'semantic_ui_js',
        'src'       => 'https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js',
        'deps'      => array('jquery'),
        'ver'       => HB_VERSION,
        'in_footer' => false,
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
    'bee'             => array(
        'hook'      => 'wp_enqueue_scripts',
        'handle'    => $bee_handle,
        'src'       => 'https://app-rsrc.getbee.io/plugin/BeePlugin.js',
        'deps'      => '',
        'ver'       => HB_VERSION,
        'in_footer' => true,
    ),
    'bee_app'         => array(
        'hook'      => 'wp_enqueue_scripts',
        'handle'    => $config['prefix'] . 'bee_app',
        'src'       => dirname(plugin_dir_url(__FILE__)) . '/js/bee-app.js',
        'deps'      => array($bee_handle),
        'ver'       => HB_VERSION,
        'in_footer' => true,
    ),
    /*
    'uikit'       => array(
        'hook'      => 'wp_enqueue_scripts',
        'handle'    => $config['prefix'] . 'uikit_js',
        'src'       => 'https://cdn.jsdelivr.net/npm/uikit@3.5.5/dist/js/uikit.min.js',
        'deps'      => array(),
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
    'jasny'           => array(
        'hook'      => 'wp_enqueue_scripts',
        'handle'    => $config['prefix'] . 'jasny_js',
        'src'       => '//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/4.0.0/js/jasny-bootstrap.min.js',
        'deps'      => array('jquery'),
        'ver'       => HB_VERSION,
        'in_footer' => true,
    ),
    */
);

return $scripts;