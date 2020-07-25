<?php
/**
 * Configurações da Tag
 */
$config = include plugin_dir_path( __FILE__ ) . '/config.php';

$tag = array(
    'name'          => $config['prefix'] . 'tag',
    'label'         => 'Tags',
    'singular_name' => 'Tag',
);

return $tag;