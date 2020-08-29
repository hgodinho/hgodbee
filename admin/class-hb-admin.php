<?php
/**
 * Admin configurations
 * @since 0.4.0
 */
/**
 * Require vendor/hgod/classes/class-hgod-tax.php
 */
if ( ! class_exists('HGod_Admin') ){
    require_once dirname(__FILE__, 2) . '/vendor/hgod/classes/class-hgod-admin.php';
}


/**
 * HB_Admin
 */
class HB_Admin {
    /**
     * Prefix
     *
     * @var string
     */
    protected $prefix;

    /**
     * Admin Menu 
     *
     * @var array
     */
    public $admin_menu;

    /**
     * Admin Submenu
     *
     * @var array
     */
    public $admin_submenu;

    /**
     * Settings
     *
     * @var array
     */
    public $settings;

    /**
     * Sections
     *
     * @var array
     */
    public $sections;

    /**
     * Fields
     *
     * @var array
     */
    public $fields;

    /**
     * Constructor
     */
    public function __construct($prefix) {
        $this->prefix = $prefix;
    }

    /**
     * Set Admin
     *
     * Configura as opções do menu e settings no admin e
     * cria uma nova instância da classe HGod_Admin
     *
     * @return void
     */
    public function set_admin() {

        /**
         * Prefixo
         */
        $prefix = $this->prefix;

        /**
         * Admin Menu
         */
        $admin_menu = array(
            'title'      => 'Configuração Bee',
            'menu_title' => 'Bee',
            'capability' => 'edit_posts', // Capabilities.
            'menu_slug'  => $prefix . 'menu',
            'callback'   => array($this, 'options_page'), // Callback. @god alterar aqui
            'position'   => 3, // Position on Menu.
        );
        $this->admin_menu = $admin_menu;

        /**
         * Admin Submenus
         */
        $admin_submenu = array( // resolver includes do cpt e da tax
            'templates' => array(
                'parent_slug' => $admin_menu['menu_slug'],
                'page_title'  => 'Templates',
                'menu_title'  => 'Templates',
                'menu_slug'   => 'edit.php?post_type=' . $prefix . 'templates',
            ),
            'categoria' => array(
                'parent_slug' => $admin_menu['menu_slug'],
                'page_title'  => 'Categorias',
                'menu_title'  => 'Categorias',
                'menu_slug'   => 'edit-tags.php?taxonomy=' . $prefix . 'tax' . '&post_type=' . $prefix . 'templates',
            ),
            'tags'      => array(
                'parent_slug' => $admin_menu['menu_slug'],
                'page_title'  => 'Tags',
                'menu_title'  => 'Tags',
                'menu_slug'   => 'edit-tags.php?taxonomy=' . $prefix . 'tag' . '&post_type=' . $prefix . 'templates',
            ),
        );
        $this->admin_submenu = $admin_submenu;

        /**
         * Settings
         */
        $settings = array(
            'option_group' => $prefix . 'settings_group',
            'option_name'  => $prefix . 'settings_name',
        );

        /**
         * Settings Sections
         */
        $sections = array(
            array(
                'id'       => $prefix . 'config_bee',
                'title'    => 'Configurações do Plugin Bee',
                'callback' => array($this, 'settings_section_callback'),
                'page'     => $settings['option_group'],
            ),
        );

        /**
         * Fields
         */
        $fields = array(
            'id'     => array(
                'id'       => $prefix . 'id',
                'title'    => 'Client ID',
                'callback' => array($this, 'client_id'), // @god CALLBACK
                'page'     => $settings['option_group'],
                'section'  => $sections[0]['id'],
            ),
            'secret' => array(
                'id'       => $prefix . 'secret',
                'title'    => 'Client Secret',
                'callback' => array($this, 'client_secret'), // @god CALLBACK
                'page'     => $settings['option_group'],
                'section'  => $sections[0]['id'],
            ),
            'uid'    => array(
                'id'       => $prefix . 'uid',
                'title'    => 'Client UID',
                'callback' => array($this, 'client_uid'), // @god CALLBACK
                'page'     => $settings['option_group'],
                'section'  => $sections[0]['id'],
            ),
        );
        $this->fields          = $fields;
        $sections[0]['fields'] = $fields;
        $this->sections        = $sections;
        $settings['sections']  = $sections;
        $this->settings        = $settings;
    }

    /**
     * Init class HGod_Admin
     *
     * @return void
     */
    public function init() {
        $prefix        = $this->prefix;
        $admin_menu    = $this->admin_menu;
        $admin_submenu = $this->admin_submenu;
        $settings      = $this->settings;
        //$sections = $sections
        $admin = new HGod_Admin($admin_menu, $admin_submenu, $settings);
    }

    /**
     * Settings Section Callback
     *
     * @return void
     */
    public function settings_section_callback() {
        esc_html_e('Insira o client id, secret e uid nos campos abaixo', 'hgodbee');
    }

    /**
     * Client ID
     *
     * @return void
     */
    public function client_id() {
        $settings = $this->settings;
        $fields   = $this->fields;
        $option   = $settings['option_name'];
        $id       = $fields['id']['id'];
        $name     = $option . '[' . $id . ']';
        $options  = get_option($option);
        $this->render_input($id, $name, $options[$id]);
    }

    /**
     * Client Secret
     */
    public function client_secret() {
        $settings = $this->settings;
        $fields   = $this->fields;
        $option   = $settings['option_name'];
        $id       = $fields['secret']['id'];
        $name     = $option . '[' . $id . ']';
        $options  = get_option($option);
        $this->render_input($id, $name, $options[$id]);
    }

    /**
     * Client UID
     */
    public function client_uid() {
        $settings = $this->settings;
        $fields   = $this->fields;
        $option   = $settings['option_name'];
        $id       = $fields['uid']['id'];
        $name     = $option . '[' . $id . ']';
        $options  = get_option($option);
        $this->render_input($id, $name, $options[$id]);
    }

    /**
     * Render Input
     *
     * @param string $id | Id.
     * @param string $name | Name.
     * @param string $value | Value.
     * @return void
     */
    public function render_input($id, $name, $value) {
        ?>
        <input type="text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>"
            value="<?php echo esc_attr($value); ?>">
        <?php
}

    /**
     * Options Page
     *
     * @return void
     */
    public function options_page() {
        //$url = get_site_url(null, '/bee-email');
        $url = get_post_type_archive_link($this->prefix . 'templates');
        ?>
        <div class="wrap">
            <div id="notification-area">
            </div>
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1><small><?php echo esc_html(HB_VERSION); ?></small>

            <br>
            <a href="<?php echo esc_url($url); ?>">Ir para o editor</a>
            <form action='options.php' method='post'>
                <?php
        if (!current_user_can('manage_options')) {
            echo '<fieldset disabled>';
        } else {
            echo '<fieldset>';
        }
        settings_fields($this->settings['option_group']);
        do_settings_sections($this->settings['option_group']);
        ?>
                <input type="submit" id="submit" class="btn btn-primary" value="Salvar alterações" />
                <input type="button" id="get_token" class="btn btn-secondary" name="get_token" value="Gerar token" />
                </fieldset>
            </form>
        </div>
        <?php
    }

}
