<?php
/**
 * @wordpress-plugin
 * Plugin Name: HGodBee
 * Description: Integração do beeplugin com o wordpress.org
 * Version: 0.5.1
 * Author: hgodinho
 * Author URI: hgodinho.com
 * Text Domain: hgodbee
 *
 * @package HGodBee
 * @author Hgodinho
 * @copyright 2020 Henrique Godinho
 * @link https://docs.beeplugin.io/initializing-bee-plugin/
 */

/**
 * Requires
 */
require dirname(__FILE__) . '/vendor/hgod/classes/class-hgod-loads.php';
require dirname(__FILE__) . '/vendor/hgod/classes/class-hgod-tax.php';

require dirname(__FILE__) . '/inc/beefree/BeeFree.php';
require_once dirname(__FILE__) . '/templates/parts/bee-plugin-notification.php';
include_once dirname(__FILE__) . '/functions/hgodbee-ajax.php';

require_once dirname(__FILE__) . '/admin/class-hb-admin.php';
require_once dirname(__FILE__) . '/class/class-hb-cpt.php';

/**
 * Classe principal
 */
class HGodBee {
    /**
     * Instância
     *
     * @var object
     */
    private static $instance;

    /**
     * Armazena as configuraçõe gerais do plugin
     *
     * @var array
     */
    private $config;

    /**
     * Versão
     *
     * @var string
     */
    private $version;

    /**
     * Text Domain
     *
     * @var string
     */
    private $txt_domain;

    /**
     * Plugin di
     *
     * @var string
     */
    private $plugin_dir;

    /**
     * Prefixo
     *
     * @var string
     */
    public $prefix = 'hgodbee_';

    /**
     * Post-type config array
     *
     * @var array
     */
    protected $cpt;

    /**
     * Taxonomy [Categoria] config array
     *
     * @var array
     */
    protected $tax;

    /**
     * Taxonomy [Tag] config array
     *
     * @var array
     */
    protected $tag;

    /**
     * Scripts config array
     *
     * @var array
     */
    protected $scripts;

    /**
     * Styles config array
     *
     * @var array
     */
    protected $styles;

    /**
     * Email page
     *
     * @var string
     */
    protected $email_page;

    /**
     * Templates array
     *
     * @var array
     */
    protected $templates;

    /**
     * Retorna instância da classe
     *
     * @return class $instance Instância da classe HGodBee
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor
     */
    private function __construct() {
        define('HB_PREFIX', $this->prefix);
        $this->config();

        if (class_exists('HGod_tax')) {
            $this->set_tax();
        }

        if (class_exists('HB_Cpt')) {   
            $cpt = new HB_Cpt($this->prefix);
            $cpt->set_cpt();
            $cpt->init();
        }

        if ( class_exists('HB_Admin')) {
            $admin = new HB_Admin($this->prefix);
            $admin->set_admin();
            $admin->init();
        }

        if (class_exists('HGod_loads')) {
            $this->set_loads();
        }

        $slug_exists = $this->the_slug_exists('bee-email-editor', 'page');
        if (!$slug_exists) {
            $this->on_activation();
        }

        add_action('wp_ajax_hgodbee_save_template', 'hgodbee_save_template');
        add_action('wp_ajax_hgodbee_save', 'hgodbee_save');
        add_action('wp_ajax_hgodbee_token', 'hgodbee_token');

        add_filter('query_vars', array($this, 'custom_query_vars'));

        //register_activation_hook(__FILE__, array($this, 'on_activation'));
        //register_deactivation_hook(__FILE__, array($this, 'emakbee_desativacao'));
    }

    /**
     * Define as configurações do plugin
     *
     * @return void
     */
    private function config() {
        $plugin_data      = self::hb_file_data('class-hgodbee.php');
        $this->version    = $plugin_data['Version'];
        $this->txt_domain = $plugin_data['Text Domain'];
        $this->plugin_dir = dirname(__FILE__);
        define('HB_VERSION', $this->version);
        define('HB_TXTDOMAIN', $this->txt_domain);

        /**
         * Configurações gerais.
         */
        $this->config = include 'config/config.php';

        /**
         * Configura CPT.
         */
        //$this->cpt = include 'config/cpt-config.php';

        /**
         * Configura Taxonomias.
         */
        $this->tax = include 'config/tax-config.php';

        /**
         * Configura Tags
         */
        $this->tag = include 'config/tag-config.php';

        /**
         * Configura scripts
         */
        /**
         * bootstrap
         */
        $this->scripts = include 'config/scripts-config.php';

        /**
         * Configura estilos
         */
        /**
         * bootstrap
         */
        $this->styles = include 'config/styles-config.php';

        /**
         * Email Editor Page
         */
        $this->email_page = array(
            'post_title'   => 'Bee Email Editor',
            'post_content' => ' ',
            'post_status'  => 'private',
            //'post_author' => get_current_user_ID(),
            'post_type'    => 'page',
            //'page_template' => $this->templates['admin/emaklabin_bee-email-page.php'],
        );
    }

    /**
     * Custom query vars
     *
     * @param array $qvars
     * @return array $qvars modified with included query vars
     */
    public function custom_query_vars($qvars) {
        $qvars[] = 'action';
        return $qvars;
    }

    /**
     * Callbacks na ativação do plugin
     *
     * @return void
     */
    private function on_activation() {
        $email_page = $this->email_page;
        //$email      = $this->new_post($email_page);
        $post = wp_insert_post($email_page);
        HGodBee::hb_log($post, 'serasefoi');
    }

    /**
     * Set Loads
     *
     * Define os scripts e styles que farão parte da aplicação e cria
     * uma nova instância da classe HGod_Loads
     *
     * @return void
     */
    public function set_loads() {
        $args = array(
            'scripts' => $this->scripts,
            'styles'  => $this->styles,
        );

        $loads = new HGod_Loads($args);
        add_action('admin_enqueue_scripts', array($this, 'admin_localize_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'public_localize_scripts'));
    }

    /**
     * Localize Scripts for use on ajax-admin.js
     *
     * @return void
     */
    public function admin_localize_scripts() {
        $ajax_object = array(
            'ajax_url'    => admin_url('admin-ajax.php'),
            'nonce_admin' => wp_create_nonce($this->config['prefix'] . 'admin'),
        );
        $localized = wp_localize_script($this->scripts['admin_ajax']['handle'], $this->config['prefix'] . 'object', $ajax_object);
    }

    /**
     * Localize Scripts for use on bee-app.js
     *
     * @return void
     */
    public function public_localize_scripts() {
        $ajax_object = array(
            'token'                  => get_option($this->config['prefix'] . 'token'),
            'ajax_url'               => admin_url('admin-ajax.php'),
            'nonce_send'             => wp_create_nonce($this->config['prefix'] . 'send'),
            'nonce_save'             => wp_create_nonce($this->config['prefix'] . 'save'),
            'nonce_save_as_template' => wp_create_nonce($this->config['prefix'] . 'save_as_template'),
        );
        $localized = wp_localize_script($this->scripts['bee_app']['handle'], $this->config['prefix'] . 'object', $ajax_object);
    }

    /**
     * Define a taxonomia e tag do template e cria
     * uma nova instância da classe HGod_Tax
     *
     * @return void
     */
    public function set_tax() {
        $args = array(
            array(
                'name'       => $this->tax['name'],
                'post_types' => array($this->cpt['name']),
                'labels'     => array(
                    'name'          => $this->tax['label'],
                    'singular_name' => $this->tax['singular_name'],
                    'menu_name'     => $this->tax['label'],
                ),
                'args'       => array(
                    'hierarchical' => true,
                ),
            ),
            array(
                'name'       => $this->tag['name'],
                'post_types' => array($this->cpt['name']),
                'labels'     => array(
                    'name'          => $this->tag['label'],
                    'singular_name' => $this->tag['singular_name'],
                    'menu_name'     => $this->tag['label'],
                ),
                'args'       => array(
                    'hierarchical' => false,
                ),
            ),
        );
        $tax = new HGod_Tax($args);
    }

    /**
     * Define o Custom post-type Template Bee e cria
     * uma noa instância da classe HGod_Cpt
     *
     * @return void
     * @deprecated 0.5.0
     */
    public function set_cpt() {
        /*
        $args = array(
            array(
                'name' => $this->cpt['name'],
                'args' => array(
                    'label'        => $this->cpt['label'],
                    'labels'       => array(
                        'name'          => $this->cpt['label'],
                        'singular_name' => $this->cpt['label'],
                    ),
                    'supports'     => array('title', 'editor', 'revisions', 'author', 'excerpt', 'page-attributes', 'thumbnail', 'custom-fields'),
                    'taxonomies'   => array($this->tax['name'], $this->tag['name']),
                    'public'       => false,
                    'show_in_menu' => false,
                ),
            ),
        );
        $cpt = new HGod_Cpt($args);
        */
        _deprecated_function( __FUNCTION__, '0.5.0', 'nothing' );
        HGodBee::hb_log(__FUNCTION__, 'deprecated function', __CLASS__, __METHOD__, __LINE__);
    }

    /**
     * Define o template para ser usado no arquivo de $cpt['name']
     * @deprecated 0.5.0
     */
    public function set_custom_archive_template($archive_template) {
        /*
        global $post;
        if (is_post_type_archive($this->cpt['name'])) {
            $archive_template = $this->cpt['archive_template'];
        }
        return $archive_template;
        */
        _deprecated_function( __FUNCTION__, '0.5.0', 'nothing' );
        HGodBee::hb_log(__FUNCTION__, 'deprecated function', __CLASS__, __METHOD__, __LINE__);
    }

    /**
     * Define o template para ser usado no single de $cpt['name']
     * @deprecated 0.5.0
     */
    public function set_custom_single_template($single_template) {
        /*
        global $post;
        if (is_singular($this->cpt['name'])) {
            $single_template = $this->cpt['single_template'];
        }
        return $single_template;
        */
        _deprecated_function( __FUNCTION__, '0.5.0', 'nothing' );
        HGodBee::hb_log(__FUNCTION__, 'deprecated function', __CLASS__, __METHOD__, __LINE__);

    }

    /**
     * Cria nova página com os parâmetros passados
     *
     * @param array $args | Definições de nova página.
     * @return (int|WP_Error) The post ID on success. The value 0 or WP_Error on failure.
     */
    public function new_post($args) {
        global $wpdb;
        $defaults = array(
            'post_name'    => '',
            'post_title'   => '',
            'post_content' => '',
            'post_author'  => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            //'page_template' => $this->templates['admin/emaklabin_bee-email-page.php'],
        );
        $arg = wp_parse_args($args, $defaults);
        //HGodBee::hb_var_dump($arg, __CLASS__, __METHOD__, __LINE__, true);
        $post = wp_insert_post($arg);
        HGodBee::hb_log($post, 'serasefoi');
        return $post;
    }

    /**
     * Faz a validação se as páginas existem ou não
     *
     * @return boolean
     */
    public function the_slug_exists($post_name, $post_type) {
        global $wpdb;
        if ($wpdb->get_row("SELECT post_name FROM wp_posts WHERE post_name = '" . $post_name . "' AND post_type = '" . $post_type . "'", 'ARRAY_A')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * HB file Data
     *
     * @param string $file | File handler.
     * @return array $plugin_data | Array with plugin data presents on file header.
     */
    public static function hb_file_data($file) {
        $file        = plugin_dir_path(__FILE__) . $file;
        $plugin_data = get_file_data(
            $file,
            array(
                'Plugin Name' => 'Plugin Name',
                'Plugin Uri'  => 'Plugin Uri',
                'Description' => 'Description',
                'Version'     => 'Version',
                'Author'      => 'Author',
                'Author Uri'  => 'Author Uri',
                'Text Domain' => 'Text Domain',
            )
        );
        return $plugin_data;
    }

    /**
     * Var Dump especial que cita a classe, método, linha
     * e `die()` o WordPress por padão
     *
     * @param mixed   $var | Valor a ser debugado.
     * @param string  $class | __CLASS__.
     * @param string  $method | __METHOD__.
     * @param string  $line | __LINE__.
     * @param boolean $die | Die WordPress on true - defaults to true.
     * **obs:** para esse método funcionar mais fluidamente é recomendável criar
     * snippet de código do chamado para o método no vscode passando as
     * constantes mágicas:
     *
     * - `__CLASS__`,
     * - `__METHOD__`,
     * - `__LINE__`.
     *
     * @see https://www.php.net/manual/pt_BR/language.constants.predefined.php
     *
     * @example
     * {
     *     "Var Dump Especial": {
     *    "scope": "php",
     *    "prefix": "dump",
     *    "body": [
     *        "HGodBee::hb_var_dump(${1:any}, __CLASS__, __METHOD__, __LINE__, ${3:true})"
     *    ],
     *    "description": "Var dump especial."
     *  },
     * }
     *
     * @echo mixed
     */
    public static function hb_var_dump($var, $class, $method, $line, $die = true) {
        if (true === $die) {
            $wp = 'muerto.';
        } else {
            $wp = 'vivito.';}
        echo '<p><strong>Class: ' . $class . ' | ';
        echo 'Method: ' . $method . ' | ';
        echo 'Line: ' . $line . ' | ';
        echo 'WordPress: ' . $wp;
        echo '</strong></p>';
        var_dump($var);
        echo '<p><strong>var_dump stop</strong></p>';
        if (true === $die) {
            wp_die();
        }
    }

    /**
     * hgod_log
     */
    public function hb_log($msg, $title = '', $class = __CLASS__, $method = __METHOD__, $line = __LINE__) {
        //$error_dir = '/erro.log';
        $date = date('d.m.Y h:i:s');
        $msg  = print_r($msg, true);
        $log  = $method . " @linha-" . $line . " | " . $title . "\n" . $msg . "\n";
        error_log($log);
    }

}

/**
 * Inicia o plugin.
 */
HGodBee::get_instance();