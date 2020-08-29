<?php
/**
 * Scripts & Styles Configurations
 * @since 0.7.0
 */

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

	public $wp_enqueue_scripts;
	public $wp_enqueue_styles;
	public $admin_enqueue_scripts;
	public $hgodbee_menu;
	public $hgodbee_menu_styles;

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

		$this->wp_enqueue_scripts = array(
			'fomantic_ui' => array(
				'hook'      => 'wp_enqueue_scripts',
				'handle'    => $prefix . 'fomantic_ui_js',
				'src'       => 'https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.6/dist/semantic.min.js',
				'deps'      => array('jquery'),
				'ver'       => HB_VERSION,
				'in_footer' => false,
			),
			'bee'         => array(
				'hook'      => 'wp_enqueue_scripts',
				'handle'    => $prefix . 'core_bee_js',
				'src'       => 'https://app-rsrc.getbee.io/plugin/BeePlugin.js',
				'deps'      => '',
				'ver'       => HB_VERSION,
				'in_footer' => true,
			),
			'bee_app'     => array(
				'hook'      => 'wp_enqueue_scripts',
				'handle'    => $prefix . 'app_js',
				'src'       => dirname(plugin_dir_url(__FILE__)) . '/js/bee-app.js',
				'deps'      => array($prefix . 'core_bee_js', 'jquery'),
				'ver'       => HB_VERSION,
				'in_footer' => true,
			),
			'tagify'      => array(
				'hook'      => 'wp_enqueue_scripts',
				'handle'    => $prefix . 'tagify_js',
				'src'       => dirname(plugin_dir_url(__FILE__)) . '/node_modules/@yaireo/tagify/dist/jQuery.tagify.min.js',
				'deps'      => array($prefix . 'core_bee_js', 'jquery'),
				'ver'       => HB_VERSION,
				'in_footer' => true,
			),
			'jszip'       => array(
				'hook'      => 'wp_enqueue_scripts',
				'handle'    => $prefix . 'jszip_js',
				'src'       => dirname(plugin_dir_url(__FILE__)) . '/node_modules/jszip/dist/jszip.min.js',
				'deps'      => array($prefix . 'app_js'),
				'ver'       => HB_VERSION,
				'in_footer' => true,
			),
			'saveas'      => array(
				'hook'      => 'wp_enqueue_scripts',
				'handle'    => $prefix . 'saveas_js',
				'src'       => dirname(plugin_dir_url(__FILE__)) . '/node_modules/jszip/vendor/FileSaver.js',
				'deps'      => array($prefix . 'app_js', $prefix . 'jszip_js'),
				'ver'       => HB_VERSION,
				'in_footer' => true,
			),
			'feather'     => array(
				'hook'      => 'wp_enqueue_scripts',
				'handle'    => $prefix . 'feather_js',
				'src'       => dirname(plugin_dir_url(__FILE__)) . '/node_modules/feather-icons/dist/feather.min.js',
				'deps'      => '',
				'ver'       => HB_VERSION,
				'in_footer' => true,
			),
			'spectrum'    => array(
				'hook'      => 'wp_enqueue_scripts',
				'handle'    => $prefix . 'spectrum_js',
				'src'       => dirname(plugin_dir_url(__FILE__)) . '/node_modules/spectrum-colorpicker/build/spectrum-min.js',
				'deps'      => $prefix . 'app_js',
				'ver'       => HB_VERSION,
				'in_footer' => true,
			),
			'resize_sensor'    => array(
				'hook'      => 'wp_enqueue_scripts',
				'handle'    => $prefix . 'resize_sensor_js',
				'src'       => dirname(plugin_dir_url(__FILE__)) . '/node_modules/css-element-queries/src/ResizeSensor.js',
				'deps'      => $prefix . 'app_js',
				'ver'       => HB_VERSION,
				'in_footer' => true,
			),
		);

		$this->admin_enqueue_scripts = array(
			'admin_bootstrap' => array(
				'hook'      => 'admin_enqueue_scripts',
				'handle'    => $prefix . 'bootstrap_admin_js',
				'src'       => dirname(plugin_dir_url(__FILE__)) . '/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js',
				'deps'      => array('jquery'),
				'ver'       => HB_VERSION,
				'in_footer' => true,
			),
		);

		$this->hgodbee_menu = array(
			'admin_ajax'      => array(
				'hook'      => 'load-toplevel_page_hgodbee_menu',
				'handle'    => $prefix . 'admin_ajax_js',
				'src'       => dirname(plugin_dir_url(__FILE__)) . '/js/ajax-admin.js',
				'deps'      => array('jquery'),
				'ver'       => HB_VERSION,
				'in_footer' => true,
			),
			'admin_bootstrap' => array(
				'hook'      => 'admin_enqueue_scripts',
				'handle'    => $prefix . 'bootstrap_admin_js',
				'src'       => dirname(plugin_dir_url(__FILE__)) . '/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js',
				'deps'      => array('jquery'),
				'ver'       => HB_VERSION,
				'in_footer' => true,
			),
		);

		$this->wp_enqueue_styles = array(
			'fomantic_ui' => array(
				'hook'      => 'wp_enqueue_scripts',
				'handle'    => $prefix . 'fomantic_ui_css',
				'src'       => 'https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.6/dist/semantic.min.css',
				'deps'      => array(),
				'ver'       => HB_VERSION,
				'in_footer' => false,
			),
			'bee_app'     => array(
				'hook'      => 'wp_enqueue_scripts',
				'handle'    => $prefix . 'hgodbee_css',
				'src'       => dirname(plugin_dir_url(__FILE__)) . '/css/hgodbee-styles.css',
				'deps'      => array(),
				'ver'       => HB_VERSION,
				'in_footer' => false,
			),
			'tagify'      => array(
				'hook'      => 'wp_enqueue_scripts',
				'handle'    => $prefix . 'tagify_css',
				'src'       => dirname(plugin_dir_url(__FILE__)) . '/node_modules/@yaireo/tagify/dist/tagify.css',
				'deps'      => array(),
				'ver'       => HB_VERSION,
				'in_footer' => false,
			),
			'spectrum'    => array(
				'hook'      => 'wp_enqueue_scripts',
				'handle'    => $prefix . 'spectrum_css',
				'src'       => dirname(plugin_dir_url(__FILE__)) . '/css/spectrum-reload.css',
				'deps'      => array(),
				'ver'       => HB_VERSION,
				'in_footer' => false,
			),
		);

		$this->hgodbee_menu_styles = array(
			'admin_bootstrap' => array(
				'hook'      => 'load-toplevel_page_hgodbee_menu',
				'handle'    => $prefix . 'bootstrap_admin_css',
				'src'       => dirname(plugin_dir_url(__FILE__)) . '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css',
				'deps'      => array(),
				'ver'       => HB_VERSION,
				'in_footer' => false,
			),
		);
	}

	/**
	 * Init class HGod_Loads and *_localize_scripts()
	 *
	 * @return void
	 */
	public function init() {
		$prefix = $this->prefix;

		add_action('wp_enqueue_scripts', array($this, 'loop_scripts_wp'));
		add_action('wp_enqueue_scripts', array($this, 'loop_styles_wp'));

		add_action('load-toplevel_page_hgodbee_menu', array($this, 'loop_scripts_hgodbee_menu'));
		add_action('load-toplevel_page_hgodbee_menu', array($this, 'loop_styles_hgodbee_menu'));

		add_action('admin_enqueue_scripts', array($this, 'admin_localize_scripts'));
		add_action('wp_enqueue_scripts', array($this, 'public_localize_scripts'));

		add_action('wp_print_scripts', array($this, 'remove_styles'));
		add_action('wp_print_scripts', array($this, 'remove_scripts'));

	}

	/**
	 * Loop Scripts WP
	 *
	 * @return void
	 */
	public function loop_scripts_wp() {
		$wp_enqueue_scripts = $this->wp_enqueue_scripts;
		$prefix             = $this->prefix;
		if (is_singular($prefix . 'templates') || is_post_type_archive($prefix . 'templates')) {
			foreach ($wp_enqueue_scripts as $script) {
				$done = wp_register_script(
					$script['handle'],
					$script['src'],
					$script['deps'],
					$script['ver'],
					$script['in_footer']
				);
				if (!$done) {
					HGodBee::hb_var_dump($script['handle'] . '_not-registered', __CLASS__, __METHOD__, __LINE__, true);
				}
				wp_enqueue_script($script['handle']);
			}
		}
	}

	/**
	 * Loop Scripts no admin
	 *
	 * @return void
	 */
	public function loop_scripts_hgodbee_menu() {
		$hgodbee_menu = $this->hgodbee_menu;
		$prefix       = $this->prefix;
		foreach ($hgodbee_menu as $script) {
			$done = wp_register_script(
				$script['handle'],
				$script['src'],
				$script['deps'],
				$script['ver'],
				$script['in_footer']
			);
			if (!$done) {
				HGodBee::hb_var_dump($script['handle'] . '_not-registered', __CLASS__, __METHOD__, __LINE__, true);
			}
			wp_enqueue_script($script['handle']);
		}
	}

	/**
	 * Loop Styles no admin
	 *
	 * @return void
	 */
	public function loop_styles_hgodbee_menu() {
		$styles = $this->hgodbee_menu_styles;
		$prefix = $this->prefix;

		foreach ($styles as $style) {
			//HGodBee::hb_var_dump($style, __CLASS__, __METHOD__, __LINE__, true);
			$done = wp_register_style(
				$style['handle'],
				$style['src'],
				$style['deps'],
				$style['ver'],
				$style['in_footer']
			);
			if (!$done) {
				HGodBee::hb_var_dump($style['handle'] . '_not-registered', __CLASS__, __METHOD__, __LINE__, true);
			}
			wp_enqueue_style($style['handle']);
		}
	}

	/**
	 * Loop Styles
	 *
	 * @return void
	 */
	public function loop_styles_wp() {
		$styles = $this->wp_enqueue_styles;
		$prefix = $this->prefix;
		//HGodBee::hb_var_dump($styles, __CLASS__, __METHOD__, __LINE__, true);
		if (is_singular($prefix . 'templates') || is_post_type_archive($prefix . 'templates')) {
			foreach ($styles as $style) {
				$done = wp_register_style(
					$style['handle'],
					$style['src'],
					$style['deps'],
					$style['ver'],
					$style['in_footer']
				);
				if (!$done) {
					HGodBee::hb_var_dump($style['ver'], __CLASS__, __METHOD__, __LINE__, true);
				}
				wp_enqueue_style($style['handle']);

			}
		}
	}

	/**
	 * Remove Scripts
	 *
	 * @return void
	 */
	public function remove_scripts() {
		$prefix = $this->prefix;
		if (is_post_type_archive($prefix . 'templates') || $prefix . 'templates' === get_post_type()) {
			wp_dequeue_script('ui-a11y.js');
			wp_deregister_script('ui-a11y.js');
		}
	}

	/**
	 * Remove Styles
	 *
	 * @return void
	 */
	public function remove_styles() {
		$prefix = $this->prefix;
		if (is_post_type_archive($prefix . 'templates') || $prefix . 'templates' === get_post_type()) {
			wp_dequeue_style('stylesheet');
			wp_dequeue_style('childstyle');
			wp_dequeue_style('default_style');
			wp_deregister_style('stylesheet');
			wp_deregister_style('childstyle');
			wp_deregister_style('default_style');
		}
	}

	/**
	 * Localize Scripts for use on ajax-admin.js
	 *
	 * @return void
	 */
	public function admin_localize_scripts() {
		$prefix      = $this->prefix;
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
		$prefix      = $this->prefix;
		$ajax_object = array(
			'token'                  => get_option($prefix . 'token'),
			'ajax_url'               => admin_url('admin-ajax.php'),
			'archive'                => get_post_type_archive_link(HB_PREFIX . 'templates'),
			'nonce_send'             => wp_create_nonce($prefix . 'send'),
			'nonce_save'             => wp_create_nonce($prefix . 'save'),
			'nonce_save_as_template' => wp_create_nonce($prefix . 'save_as_template'),
			'nonce_delete'           => wp_create_nonce($prefix . 'delete'),
			'nonce_save_colors'      => wp_create_nonce($prefix . 'save_colors'),
			'nonce_autosave'      => wp_create_nonce($prefix . 'autosave'),
		);
		$localized = wp_localize_script($prefix . 'app_js', $prefix . 'object', $ajax_object);
	}
}