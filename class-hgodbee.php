<?php
/**
 * Hgodbee
 *
 * Plugin.
 *
 * @wordpress-plugin
 * Plugin Name: HGodBee
 * Description: Integração do beeplugin com o wordpress.org
 * Version: 0.10.2
 * Author: hgodinho
 * Author URI: hgodinho.com
 * Text Domain: hgodbee
 * GitHub Plugin URI: https://github.com/hgodinho/hgodbee
 *
 * @package HGodBee
 * @author Hgodinho
 * @copyright 2020 Henrique Godinho
 * @link https://docs.beeplugin.io/initializing-bee-plugin/
 */

/**
 * Requires
 */
require dirname( __FILE__ ) . '/inc/beefree/BeeFree.php';
require_once dirname( __FILE__ ) . '/templates/parts/bee-plugin-notification.php';
require_once dirname( __FILE__ ) . '/functions/hgodbee-ajax.php';

require_once dirname( __FILE__ ) . '/admin/class-hb-admin.php';
require_once dirname( __FILE__ ) . '/class/class-hb-cpt.php';
require_once dirname( __FILE__ ) . '/class/class-hb-tax.php';
require_once dirname( __FILE__ ) . '/class/class-hb-scripts.php';

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
	 * Comece do Zero! (template)
	 *
	 * @var string
	 */
	protected $start_page;

	/**
	 * Templates array
	 *
	 * @var array
	 */
	protected $templates;

	/**
	 * Custom post type
	 *
	 * @var array
	 */
	protected $cpt;

	/**
	 * Retorna instância da classe
	 *
	 * @return class $instance Instância da classe HGodBee
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Construtor
	 */
	private function __construct() {

		$this->config();

		$prefix = $this->prefix;

		if ( class_exists( 'HB_Tax' ) ) {
			$tax = new HB_Tax( $prefix );
			$tax->set_tax();
			$tax->init();
		}

		if ( class_exists( 'HB_Cpt' ) ) {
			$cpt = new HB_Cpt( $prefix );
			$cpt->set_cpt();
			$cpt->init();
			$this->cpt = $cpt;
		}

		if ( class_exists( 'HB_Admin' ) ) {
			$admin = new HB_Admin( $prefix );
			$admin->set_admin();
			$admin->init();
		}

		if ( class_exists( 'HB_Scripts' ) ) {
			$scripts = new HB_Scripts( $prefix );
			$scripts->set_loads();
			$scripts->init();
		}

		$slug_exists = $this->the_slug_exists( 'comece-do-zero', HB_PREFIX . 'templates' );
		if ( ! $slug_exists ) {
			$this->new_post( $this->start_page );
		} else {
			$blank = get_page_by_path( 'comece-do-zero', OBJECT, HB_PREFIX . 'templates' );
			define( 'HB_BLANK_ID', $blank->ID );
		}

		add_action( 'wp_ajax_hgodbee_token', 'hgodbee_token' );
		add_action( 'wp_ajax_hgodbee_save_template', 'hgodbee_save_template' );
		add_action( 'wp_ajax_hgodbee_save', 'hgodbee_save' );
		add_action( 'wp_ajax_hgodbee_save_new', 'hgodbee_save_new' );
		add_action( 'wp_ajax_hgodbee_autosave', 'hgodbee_autosave' );
		add_action( 'wp_ajax_hgodbee_delete', 'hgodbee_delete' );
		add_action( 'wp_ajax_hgodbee_save_colors', 'hgodbee_save_colors' );
		add_action( 'wp_ajax_hgodbee_save_template_star_button', 'hgodbee_save_template_star_button' );
		add_action( 'wp_ajax_hgodbee_emakbee_migracao', 'hgodbee_emakbee_migracao' );

		add_filter( 'query_vars', array( $this, 'custom_query_vars' ) );
		add_filter( 'the_title', array( $this, 'remove_private_prefix' ) );

		register_activation_hook( __FILE__, array( $this, 'on_activation' ) );
		// register_deactivation_hook(__FILE__, array($this, 'emakbee_desativacao'));
	}

	/**
	 * Define as configurações do plugin
	 *
	 * @return void
	 */
	private function config() {
		$plugin_data      = self::hb_file_data( 'class-hgodbee.php' );
		$this->version    = $plugin_data['Version'];
		$this->txt_domain = $plugin_data['Text Domain'];
		$this->plugin_dir = dirname( __FILE__ );
		define( 'HB_VERSION', $this->version );
		define( 'HB_TXTDOMAIN', $this->txt_domain );
		define( 'HB_PREFIX', $this->prefix );
		define( 'HB_DEBUG', false );

		/**
		 * Configurações gerais.
		 */
		$this->config = include 'config/config.php';

		/**
		 * Email Editor Page
		 */
		$blank = file_get_contents( dirname( __FILE__ ) . '/templates/json/blank-template.json' );
		$this->start_page = array(
			'post_title'   => 'Comece do Zero!',
			'post_content' => $blank,
			'post_status'  => 'private',
			'post_author'  => get_current_user_ID(),
			'post_type'    => HB_PREFIX . 'templates',
		);
	}

	/**
	 * Custom query vars
	 *
	 * @param array $qvars initial.
	 * @return array $qvars modified with included query vars.
	 */
	public function custom_query_vars( $qvars ) {
		$qvars[] = 'action';
		return $qvars;
	}

	/**
	 * Remove private prefix
	 *
	 * Remove a string 'Privado: ' do $title do cpt template_bee.
	 *
	 * @param $title Titulo.
	 * @return $title Titulo.
	 */
	public function remove_private_prefix( $title ) {

		$cpt = $this->cpt->cpts[0]['name'];

		if ( get_post_type() === $cpt ) {
			$title = esc_attr( $title );

			$findthesse = array(
				'#Privado:#',
			);

			$replacewith = array(
				'',
			);

			$title = preg_replace( $findthesse, $replacewith, $title );
			return $title;
		} else {
			return $title;
		}

	}

	/**
	 * Callbacks na ativação do plugin
	 *
	 * @return void
	 */
	public function on_activation() {
		$start_page = $this->start_page;
		$post       = wp_insert_post( $start_page );
		flush_rewrite_rules();
	}

	/**
	 * Cria nova página com os parâmetros passados
	 *
	 * @param array $args | Definições de nova página.
	 */
	public function new_post( $args ) {
		global $wpdb;
		$defaults = array(
			'post_name'    => '',
			'post_title'   => '',
			'post_content' => '',
			'post_author'  => '',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		);
		$arg      = wp_parse_args( $args, $defaults );
		$post     = wp_insert_post( $arg );
	}

	/**
	 * Faz a validação se as páginas existem ou não
	 *
	 * @param string $post_name Nome do post.
	 * @param string $post_type Tipo de post.
	 * @return boolean
	 */
	public function the_slug_exists( $post_name, $post_type ) {
		global $wpdb;
		if ( $wpdb->get_row( "SELECT post_name FROM wp_posts WHERE post_name = '" . $post_name . "' AND post_type = '" . $post_type . "'", 'ARRAY_A' ) ) {
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
	public static function hb_file_data( $file ) {
		$file        = plugin_dir_path( __FILE__ ) . $file;
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
	public static function hb_var_dump( $var, $class, $method, $line, $die = true ) {
		if ( true === $die ) {
			$wp = 'muerto.';
		} else {
			$wp = 'vivito.';}
		echo '<p><strong>Class: ' . $class . ' | ';
		echo 'Method: ' . $method . ' | ';
		echo 'Line: ' . $line . ' | ';
		echo 'WordPress: ' . $wp;
		echo '</strong></p>';
		var_dump( $var );
		echo '<p><strong>var_dump stop</strong></p>';
		if ( true === $die ) {
			wp_die();
		}
	}

	/**
	 * Bb log
	 *
	 * @param mixed  $msg Erro.
	 * @param string $title Título do erro.
	 * @param string $class Classe do erro.
	 * @param string $method Método do erro.
	 * @param string $line Linha do erro.
	 * @return void
	 */
	public function hb_log( $msg, $title = '', $class = __CLASS__, $method = __METHOD__, $line = __LINE__ ) {
		$date = gmdate( 'd.m.Y h:i:s' );
		if ( is_bool( $msg ) ) {
			$msg = print( $msg );
		} else {
			$msg = print_r( $msg, true );
		}
		$log = $method . ' @linha-' . $line . ' | ' . $title . "\n" . $msg . "\n";
		error_log( $log );
	}

}

/**
 * Inicia o plugin.
 */
HGodBee::get_instance();
