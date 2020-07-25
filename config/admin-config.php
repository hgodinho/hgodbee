<?php
/**
 * Configurações do admin
 */

 $config = include plugin_dir_path( __FILE__ ) . '/config.php';

$admin = array(
    'admin_menu' => array(
        'title'      => 'Configuração Bee',
        'menu_title' => 'Bee',
        'menu_slug'  => $config['prefix'] . 'menu',
    ),
    'settings'   => array(
        'option_group' => $config['prefix'] . 'settings_group',
        'option_name'  => $config['prefix'] . 'settings_name',
    ),
    'sections'   => array(
        'config_bee' => array(
            'id'    => $config['prefix'] . 'config_bee',
            'title' => 'Configurações do Plugin Bee',
        ),
    ),
    'fields'     => array(
        'id'     => array(
            'id'       => $config['prefix'] . 'id',
            'title'    => 'Client ID',
            //'callback' => array($this, 'client_id'),
            'callback' => array( $this, 'client_id' ),
            'page'     => $config['prefix'] . 'settings_group',
            'section'  => $config['prefix'] . 'config_bee',
        ),
        'secret' => array(
            'id'       => $config['prefix'] . 'secret',
            'title'    => 'Client Secret',
            //'callback' => array($this, 'client_secret'),
            'callback' => array( $this, 'client_secret' ),
            'page'     => $config['prefix'] . 'settings_group',
            'section'  => $config['prefix'] . 'config_bee',
        ),
        'uid'    => array(
            'id'       => $config['prefix'] . 'uid',
            'title'    => 'Client UID',
            //'callback' => array($this, 'client_uid'),
            'callback' => array( $this, 'client_uid' ),
            'page'     => $config['prefix'] . 'settings_group',
            'section'  => $config['prefix'] . 'config_bee',
        ),
    ),
);

return $admin;