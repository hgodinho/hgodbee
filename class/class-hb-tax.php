<?php
/**
 * Taxonomies Configuration
 * @since 0.6.0
 */
/**
 * Require /vendor/hgod/classes/class-hgod-tax.php
 */
if ( ! class_exists('HGod_Tax') ){
    require_once dirname(__FILE__, 2) . '/vendor/hgod/classes/class-hgod-tax.php';
}
/**
 * HB_Tax
 */
class HB_Tax {
    /**
     * Prefix
     *
     * @var string
     */
    private $prefix;

    /**
     * Taxonomies array
     *
     * @var array
     */
    public $taxonomies;

    /**
     * Constructor
     *
     * @param string $prefix
     */
    public function __construct($prefix) {
        $this->prefix = $prefix;
    }

    /**
     * Set Taxomies array
     *
     * @return void
     */
    public function set_tax() {

        $prefix = $this->prefix;

        $tax = array(
            'name'       => $prefix . 'tax',
            'post_types' => array($prefix . 'templates'),
            'labels'     => array(
                'label'         => 'Categorias',
                'singular_name' => 'Categoria',
                'label'         => 'Categorias',
            ),
            'args'       => array(
                'hierarchical' => true,
            ),
        );
        $tag = array(
            'name'       => $prefix . 'tag',
            'post_types' => array($prefix . 'templates'),
            'labels'     => array(
                'name'          => 'Tags',
                'singular_name' => 'Tag',
                'menu_name'     => 'Tags',
            ),
            'args'       => array(
                'hierarchical' => false,
            ),
        );

        $taxonomies       = array($tax, $tag);
        $this->taxonomies = $taxonomies;
    }

    /**
     * Init class HGod_Tax
     *
     * @return void
     */
    public function init() {
        if (isset($this->taxonomies)) {
            $taxonomies = $this->taxonomies;
            $tax        = new HGod_Tax($taxonomies);
        }
    }

}