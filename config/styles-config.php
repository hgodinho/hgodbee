<?php
/**
 * Configurações dos estilos a serem carregados
 */
$config = include plugin_dir_path(__FILE__) . '/config.php';

$styles = array(
    'semantic_ui'       => array(
        'hook'      => 'wp_enqueue_scripts',
        'handle'    => $config['prefix'] . 'semantic_ui_css',
        'src'       => 'https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css',
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
    'hgodbee'       => array(
        'hook'      => 'wp_enqueue_scripts',
        'handle'    => $config['prefix'] . 'hgdobee_css',
        'src'       => dirname(plugin_dir_url(__FILE__)) . '/css/hgodbee-styles.css',
        'deps'      => array(),
        'ver'       => HB_VERSION,
        'in_footer' => false,
    ),
    /*
    'uikit'       => array(
        'hook'      => 'wp_enqueue_scripts',
        'handle'    => $config['prefix'] . 'uikit_css',
        'src'       => 'https://cdn.jsdelivr.net/npm/uikit@3.5.5/dist/css/uikit.min.css',
        'deps'      => array(),
        'ver'       => HB_VERSION,
        'in_footer' => false,
    ),
    'jasny'           => array(
        'hook'      => 'wp_enqueue_scripts',
        'handle'    => $config['prefix'] . 'jasny_css',
        'src'       => '//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/4.0.0/css/jasny-bootstrap.min.css',
        'deps'      => array(),
        'ver'       => HB_VERSION,
        'in_footer' => true,
    ),
    'bootstrap'       => array(
        'hook'      => 'wp_enqueue_scripts',
        'handle'    => $config['prefix'] . 'bootstrap_css',
        'src'       => dirname(plugin_dir_url(__FILE__)) . '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css',
        'deps'      => array(),
        'ver'       => HB_VERSION,
        'in_footer' => false,
    ),
    */
);

return $styles;
