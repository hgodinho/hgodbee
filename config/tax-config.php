<?php
/**
 * Configurações da Taxonomia
 */
$config = include plugin_dir_path( __FILE__ ) . '/config.php';

$tax = array(
    'name'          => $config['prefix'] . 'tax',
    'label'         => 'Categorias',
    'singular_name' => 'Categoria',
);

return $tax;