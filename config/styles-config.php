<?php
/**
 * Configurações dos estilos a serem carregados
 */
$config = include plugin_dir_path(__FILE__) . '/config.php';

$styles = array(
    'bootstrap'       => array(
        'hook'      => 'wp_enqueue_scripts',
        'handle'    => $config['prefix'] . 'bootstrap_css',
        'src'       => dirname(plugin_dir_url(__FILE__)) . '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css',
        'deps'      => array(),
        'ver'       => HB_VERSION,
        'in_footer' => false,
    ),
    'admin_bootstrap' => array(
        'hook'      => 'load-toplevel_page_hgodbee_menu',
        'handle'    => $config['prefix'] . 'bootstrap_admin_css',
        'src'       => dirname(plugin_dir_url(__FILE__)) . '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css',
        'deps'      => array(),
        'ver'       => HB_VERSION,
        'in_footer' => false,
    ),
);

return $styles;
