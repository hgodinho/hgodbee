<?php
/**
 * Scripts & Styles Configurations
 * @since 0.7.0
 */
/**
 * Requires /vendor/hgod/classes/class-hgod-loads.php
 */
require_once dirname(__FILE__, 2) . '/vendor/hgod/classes/class-hgod-loads.php';

/**
 * HB_Scripts
 */
class HB_Scripts {

    /**
     * Prefix
     *
     * @var string
     */
    private $prefix;

    /**
     * Scripts and Styles
     *
     * @var array
     */
    public $scripts_styles;

    /**
     * Construtor
     *
     * @param string $prefix
     */
    public function __construct($prefix) {
        $this->prefix = $prefix;
    }

    /**
     * Set Loads
     *
     * Configura os arrays de scripts e styles
     *
     * @return void
     */
    public function set_loads() {

        $prefix = $this->prefix;

        $scripts = array(
            'semantic_ui'       => array(
                'hook'      => 'wp_enqueue_scripts',
                'handle'    => $prefix . 'semantic_ui_js',
                'src'       => 'https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js',
                'deps'      => array('jquery'),
                'ver'       => HB_VERSION,
                'in_footer' => false,
            ), 
            'admin_bootstrap' => array(
                'hook'      => 'admin_enqueue_scripts',
                'handle'    => $prefix . 'bootstrap_admin_js',
                'src'       => dirname(plugin_dir_url(__FILE__)) . '/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js',
                'deps'      => array('jquery'),
                'ver'       => HB_VERSION,
                'in_footer' => true,
            ),
            'admin_ajax'      => array(
                'hook'      => 'load-toplevel_page_hgodbee_menu',
                'handle'    => $prefix . 'admin_ajax_js',
                'src'       => dirname(plugin_dir_url(__FILE__)) . '/js/ajax-admin.js',
                'deps'      => array('jquery'),
                'ver'       => HB_VERSION,
                'in_footer' => true,
            ),
            'bee'             => array(
                'hook'      => 'wp_enqueue_scripts',
                'handle'    => $prefix . 'core_bee_js',
                'src'       => 'https://app-rsrc.getbee.io/plugin/BeePlugin.js',
                'deps'      => '',
                'ver'       => HB_VERSION,
                'in_footer' => true,
            ),
            'bee_app'         => array(
                'hook'      => 'wp_enqueue_scripts',
                'handle'    => $prefix . 'app_js',
                'src'       => dirname(plugin_dir_url(__FILE__)) . '/js/bee-app.js',
                'deps'      => array($prefix . 'core_bee_js', 'jquery'),
                'ver'       => HB_VERSION,
                'in_footer' => true,
            ),
            'tagify'         => array(
                'hook'      => 'wp_enqueue_scripts',
                'handle'    => $prefix . 'tagify_js',
                'src'       => dirname(plugin_dir_url(__FILE__)) . '/node_modules/@yaireo/tagify/dist/jQuery.tagify.min.js',
                'deps'      => array($prefix . 'core_bee_js', 'jquery'),
                'ver'       => HB_VERSION,
                'in_footer' => true,
            ),
        );

        $styles = array(
            'semantic_ui'       => array(
                'hook'      => 'wp_enqueue_scripts',
                'handle'    => $prefix . 'semantic_ui_css',
                'src'       => 'https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css',
                'deps'      => array(),
                'ver'       => HB_VERSION,
                'in_footer' => false,
            ),
            'admin_bootstrap' => array(
                'hook'      => 'load-toplevel_page_hgodbee_menu',
                'handle'    => $prefix . 'bootstrap_admin_css',
                'src'       => dirname(plugin_dir_url(__FILE__)) . '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css',
                'deps'      => array(),
                'ver'       => HB_VERSION,
                'in_footer' => false,
            ),
            'bee_app'       => array(
                'hook'      => 'wp_enqueue_scripts',
                'handle'    => $prefix . 'hgodbee_css',
                'src'       => dirname(plugin_dir_url(__FILE__)) . '/css/hgodbee-styles.css',
                'deps'      => array(),
                'ver'       => HB_VERSION,
                'in_footer' => false,
            ),
            'tagify'       => array(
                'hook'      => 'wp_enqueue_scripts',
                'handle'    => $prefix . 'tagify_css',
                'src'       => dirname(plugin_dir_url(__FILE__)) . '/node_modules/@yaireo/tagify/dist/tagify.css',
                'deps'      => array(),
                'ver'       => HB_VERSION,
                'in_footer' => false,
            ),
        );
        
        $scripts_styles = array(
            'scripts' => $scripts,
            'styles'  => $styles,
        );

        $this->scripts_styles = $scripts_styles;
    }


    /**
     * Init class HGod_Loads and *_localize_scripts()
     *
     * @return void
     */
    public function init() {
        if ( isset($this->scripts_styles) ) {
            $scripts_styles = $this->scripts_styles;
            $loads = new HGod_Loads($scripts_styles);
            add_action('admin_enqueue_scripts', array($this, 'admin_localize_scripts'));
            add_action('wp_enqueue_scripts', array($this, 'public_localize_scripts'));
        }
    }

    /**
     * Localize Scripts for use on ajax-admin.js
     *
     * @return void
     */
    public function admin_localize_scripts() {
        $prefix = $this->prefix;
        $ajax_object = array(
            'ajax_url'    => admin_url('admin-ajax.php'),
            'nonce_admin' => wp_create_nonce($prefix . 'admin'),
        );
        $localized = wp_localize_script($prefix . 'admin_ajax_js', $prefix . 'object', $ajax_object);
    }

    /**
     * Localize Scripts for use on bee-app.js
     *
     * @return void
     */
    public function public_localize_scripts() {
        $prefix = $this->prefix;
        $ajax_object = array(
            'token'                  => get_option($prefix . 'token'),
            'ajax_url'               => admin_url('admin-ajax.php'),
            'nonce_send'             => wp_create_nonce($prefix . 'send'),
            'nonce_save'             => wp_create_nonce($prefix . 'save'),
            'nonce_save_as_template' => wp_create_nonce($prefix . 'save_as_template'),
        );
        $localized = wp_localize_script($prefix . 'app_js', $prefix . 'object', $ajax_object);
    }
}
