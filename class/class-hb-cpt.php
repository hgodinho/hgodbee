<?php
/**
 * Custom-post-types configurations
 * @since 0.5.0
 */
/**
 * Requer a hgod/classes via composer
 */
if (!class_exists('HGod_Cpt')) {
	require_once dirname(__FILE__, 2) . '/vendor/hgod/classes/class-hgod-cpt.php';
}
/**
 * Classe para definir o(s) CPT usado(s) no plugin
 */
class HB_Cpt {
	/**
	 * Prefix
	 *
	 * @var string
	 */
	private $prefix;

	/**
	 * Custom Post Types
	 *
	 * @var array
	 * @see https://github.com/hgodinho/classes/blob/master/class-hgod-cpt.php
	 */
	public $cpts;

	/**
	 * Constructor
	 */
	public function __construct($prefix) {
		$this->prefix = $prefix;
	}

	/**
	 * Init Class
	 *
	 * @return void
	 * @see https://github.com/hgodinho/classes/blob/master/class-hgod-cpt.php
	 */
	public function init() {
		$cpts = $this->cpts;
		$cpt  = new HGod_Cpt($cpts); // the class supports more than one cpt passed at once
		add_filter('archive_template', array($this, 'set_custom_archive_template'));
		add_filter('single_template', array($this, 'set_custom_single_template'));
	}

	/**
	 * Set Custom Post Types
	 *
	 * @return void
	 */
	public function set_cpt() {
		$prefix   = $this->prefix;
		$cpt_name = $prefix . 'templates';

		$cpt = array(
			'name'             => $cpt_name,
			'args'             => array(
				'label'        => 'Bee Templates',
				'labels'       => array(
					'name'          => 'Bee Templates',
					'singular_name' => 'Bee Template',
				),
				'supports'     => array('title', 'editor', 'revisions', 'author', 'excerpt', 'page-attributes', 'thumbnail', 'custom-fields'),
				'taxonomies'   => array($prefix . 'tax', $prefix . 'tag'),
				'public'       => false,
				'show_in_menu' => false,
				'has_archive'  => 'mailing',
				'rewrite'      => array(
					'slug' => 'mailing',
				),
			),
			'archive_template' => dirname(plugin_dir_path(__FILE__)) . '/templates/archive-' . $cpt_name . '.php',
			'single_template'  => dirname(plugin_dir_path(__FILE__)) . '/templates/single-' . $cpt_name . '.php',
		);

		$this->cpts = array($cpt); // the class supports more than one cpt passed at once
	}

	/**
	 * Define o template para ser usado no arquivo.
	 */
	public function set_custom_archive_template($archive_template) {
		global $post;
		$cpts = $this->cpts;
		if (is_post_type_archive($cpts[0]['name'])) {
			$archive_template = $cpts[0]['archive_template'];
		}
		return $archive_template;
	}

	/**
	 * Define o template para ser usado no single.
	 */
	public function set_custom_single_template($single_template) {
		global $post;
		$cpts = $this->cpts;
		if (is_singular($cpts[0]['name'])) {
			$single_template = $cpts[0]['single_template'];
		}
		return $single_template;
	}

}