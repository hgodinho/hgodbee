<?php
/**
 * Custom-post type configurations
 */
$config = include plugin_dir_path( __FILE__ ) . '/config.php';

$cpt_name = $config['prefix'] . 'templates';

$plugin_dir = dirname(plugin_dir_path(__FILE__));

$cpt        = array(
    'name'             => $cpt_name,
    'label'            => 'Bee Templates',
    'archive_template' => $plugin_dir . '/templates/archive-' . $cpt_name . '.php',
    'single_template'  => $plugin_dir . '/templates/single-' . $cpt_name . '.php',
);
return $cpt;