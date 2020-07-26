<?php
/**
 * @wordpress-plugin
 * Plugin Name: HGodBee
 * Description: Integração do beeplugin com o wordpress.org
 * Version: 0.2.0
 * Author: hgodinho
 * Author URI: hgodinho.com
 * Text Domain: hgodbee
 *
 * @package HGodBee
 * @author Hgodinho
 * @copyright 2020 Henrique Godinho
 * @link https://docs.beeplugin.io/initializing-bee-plugin/
 *
 * @todo
 * - transformar as funções var_dump e file_data em uma classe especifica de debug,
 *  incluir outros métodos úteis
 * - criar função de criar template vazio no início do plugin.
 */

/**
 * Requires
 */
require dirname(__FILE__) . '/vendor/hgod/classes/class-hgod-loads.php';
require dirname(__FILE__) . '/vendor/hgod/classes/class-hgod-cpt.php';
require dirname(__FILE__) . '/vendor/hgod/classes/class-hgod-tax.php';
require dirname(__FILE__) . '/vendor/hgod/classes/class-hgod-admin.php';
require dirname(__FILE__) . '/inc/beefree/BeeFree.php';

//$ajax_functions = include 'functions/hgodbee-ajax.php';

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
     * Admin config array
     *
     * @var array
     */
    protected $admin;

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
        $this->config();

        if (class_exists('HGod_tax')) {
            $this->set_tax();
        }

        if (class_exists('HGod_cpt')) {
            $this->set_cpt();
            if (isset($this->cpt['archive_template'])) {
                add_filter('archive_template', array($this, 'set_custom_archive_template'));
            }
            if (isset($this->cpt['single_template'])) {
                add_filter('single_template', array($this, 'set_custom_single_template'));
            }
        }

        if (class_exists('HGod_admin')) {
            $this->set_admin();
        }

        if (class_exists('HGod_loads')) {
            $this->set_loads();
        }

        $slug_exists = $this->the_slug_exists('bee-email-editor', 'page');
        //HGodBee::hb_var_dump($slug_exists, __CLASS__, __METHOD__, __LINE__, true);
        if (!$slug_exists) {
            $this->on_activation();
        }

        add_action('wp_ajax_hgodbee_save_template', array($this, 'hgodbee_save_template'));
        add_action('wp_ajax_hgodbee_save', array($this, 'hgodbee_save'));
        add_action('wp_ajax_hgodbee_token', array($this, 'hgodbee_token'));

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
        $this->cpt = include 'config/cpt-config.php';

        /**
         * Configura Taxonomias.
         */
        $this->tax = include 'config/tax-config.php';

        /**
         * Configura Tags
         */
        $this->tag = include 'config/tag-config.php';

        /**
         * Configura Admin
         */
        $this->admin = include 'config/admin-config.php';

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
     * AJAX FUNTIONS!
     */
    /**
     * Retrieves the Bee Token
     *
     * You must have a developer account at https://developers.beefree.io/
     *
     * @return void
     */
    public function hgodbee_token() {
        $nonce = check_ajax_referer($this->config['prefix'] . 'admin', 'nonce');
        global $wpdb;
        $options       = get_option($this->admin['settings']['option_name']);
        $client_id     = $options[$this->admin['fields']['id']['id']];
        $client_secret = $options[$this->admin['fields']['secret']['id']];
        $beeplugin     = new BeeFree($client_id, $client_secret);
        $token         = $beeplugin->getCredentials();

        if (isset($token->access_token)) {
            $token_saved = update_option($this->config['prefix'] . 'token', $token);

            if (true === $token_saved) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>';
                echo 'Token gerado e salvo com sucesso.';
                echo '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>';
                echo 'Token NÃO salvo.';
                echo '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
        wp_die();
    }

    public function hgodbee_save_template() {
        $nonce = check_ajax_referer($this->config['prefix'] . 'save_as_template', 'nonce');
        global $wpdb;
        $json_template        = $_POST['json'];
        $template_name        = $_POST['name'];
        $template_description = $_POST['dsc'];
        $cpt                  = $this->cpt['name'];
        if (post_exists($template_name)) {
            $page_id         = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '" . $template_name . "' AND post_type = '" . $cpt . "'");
            $template_update = array(
                'ID'           => $page_id,
                'post_content' => $json_template,
                'post_excerpt' => $template_description,
            );
            wp_update_post($template_update);
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>';
            echo 'Template atualizado com sucesso: </strong>';
            echo $template_name . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            if (current_user_can('edit_posts')) {
                $saved = wp_insert_post(array(
                    'post_content' => $json_template,
                    'post_title'   => $template_name,
                    'post_excerpt' => $template_description,
                    'post_status'  => 'publish',
                    'post_type'    => $cpt,
                )
                );
                HGodBee::hb_log($saved, $cpt, __CLASS__, __METHOD__, __LINE__);
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>';
                echo 'Template criado com sucesso: </strong>';
                echo $template_name . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                echo 'sem permissão';
            }
        }
        delete_transient('emakbee_autosave');
        delete_transient('emakbee_user');
        wp_die();
    }

    public function hgodbee_save() {
        $nonce = check_ajax_referer($this->config['prefix'] . 'save', 'nonce');
        global $wpdb;
        $json_template        = $_POST['json'];
        $html_file            = $_POST['html'];
        $template_name        = $_POST['name'];
        $template_description = $_POST['dsc'];
        $categories           = $_POST['categories'];
        $cpt                  = $this->cpt['name'];
        if (post_exists($template_name)) {
            $page_id         = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '" . $template_name . "' AND post_type = '" . $cpt . "'");
            $template_update = array(
                'ID'           => $page_id,
                'post_content' => $json_template,
                'post_excerpt' => $template_description,
            );
            $saved      = wp_update_post($template_update); // return (int|WP_Error) The post ID on success. The value 0 or WP_Error on failure.
            $saved_html = update_post_meta($saved, $this->config['prefix'] . 'saved_html', $html_file); //return(int|bool) The new meta field ID if a field with the given key didn't exist and was therefore added, true on successful update, false on failure.
            foreach ($categories as $category) {
                if (!term_exists($category, $this->config['prefix'] . 'tax')) {
                    $term_added   = wp_insert_term($category, $this->config['prefix'] . 'tax'); // return (array|WP_Error) An array containing the term_id and term_taxonomy_id, WP_Error otherwise.
                    $term_related = wp_set_object_terms($saved, $term_added['term_id'], $this->config['prefix'] . 'tax', true); // return (array|WP_Error) Term taxonomy IDs of the affected terms or WP_Error on failure.
                } else {
                    $term         = get_term_by('name', $category, $this->config['prefix'] . 'tax');
                    $term_related = wp_set_object_terms($saved, $term->term_id, $this->config['prefix'] . 'tax', true); // return (array|WP_Error) Term taxonomy IDs of the affected terms or WP_Error on failure.
                }
            }

            echo '<div class="alert alert-success alert-dismissible fade show" data-dismiss="alert" role="alert"><strong>';
            echo 'Template atualizado com sucesso: </strong>';
            echo $template_name . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            if (current_user_can('edit_posts')) {
                $saved = wp_insert_post(array(
                    'post_content' => $json_template,
                    'post_title'   => $template_name,
                    'post_excerpt' => $template_description,
                    'post_status'  => 'publish',
                    'post_type'    => $cpt,
                )
                );
                $saved_html = update_post_meta($saved, $this->config['prefix'] . 'saved_html', $html_file);
                echo '<div class="alert alert-success alert-dismissible fade show" data-dismiss="alert" role="alert"><strong>';
                echo 'Template criado com sucesso: </strong>';
                echo $template_name . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                echo 'sem permissão';
            }
        }
        delete_transient('emakbee_autosave');
        delete_transient('emakbee_user');
        wp_die();
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
     * Set Admin
     *
     * Configura as opções do menu e settings no admin e
     * cria uma nova instância da classe HGod_Admin
     *
     * @return void
     */
    public function set_admin() {
        $args = array(
            array(
                'admin_menu'    => array(
                    'title'      => $this->admin['admin_menu']['title'], // Page Title.
                    'menu_title' => $this->admin['admin_menu']['menu_title'], // Menu Title.
                    'capability' => 'edit_posts', // Capabilities.
                    'menu_slug'  => $this->admin['admin_menu']['menu_slug'], // Menu Slug.
                    'callback'   => array($this, 'options_page'), // Callback.
                    'position'   => 3, // Position on Menu.
                ),
                'admin_submenu' => array(
                    array(
                        'parent_slug' => $this->admin['admin_menu']['menu_slug'],
                        'page_title'  => $this->cpt['label'],
                        'menu_title'  => $this->cpt['label'],
                        'menu_slug'   => 'edit.php?post_type=' . $this->cpt['name'],
                    ),
                    array(
                        'parent_slug' => $this->admin['admin_menu']['menu_slug'],
                        'page_title'  => $this->tax['label'],
                        'menu_title'  => $this->tax['label'],
                        'menu_slug'   => 'edit-tags.php?taxonomy=' . $this->tax['name'] . '&post_type=' . $this->cpt['name'],
                    ),
                    array(
                        'parent_slug' => $this->admin['admin_menu']['menu_slug'],
                        'page_title'  => $this->tag['label'],
                        'menu_title'  => $this->tag['label'],
                        'menu_slug'   => 'edit-tags.php?taxonomy=' . $this->tag['name'] . '&post_type=' . $this->cpt['name'],
                    ),
                ),
                'settings'      => array(
                    array(
                        'option_group' => $this->admin['settings']['option_group'],
                        'option_name'  => $this->admin['settings']['option_name'],
                        'sections'     => array(
                            array(
                                'id'       => $this->admin['sections']['config_bee']['id'],
                                'title'    => $this->admin['sections']['config_bee']['title'],
                                'callback' => array($this, 'settings_section_callback'),
                                'page'     => $this->admin['settings']['option_group'],
                                'fields'   => $this->admin['fields'],
                            ),
                        ),
                    ),
                ),
                'txt_domain'    => HB_TXTDOMAIN,
            ),
        );
        $admin = new HGod_Admin($args);
        //HGodBee::hb_var_dump($admin, __CLASS__, __METHOD__, __LINE__, true);
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
     */
    public function set_cpt() {
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
                    'show_in_menu' => false,
                ),
            ),
        );
        $cpt = new HGod_Cpt($args);

    }

    /**
     * Define o template para ser usado no arquivo de $cpt['name']
     */
    public function set_custom_archive_template($archive_template) {
        global $post;
        if (is_post_type_archive($this->cpt['name'])) {
            $archive_template = $this->cpt['archive_template'];
        }
        return $archive_template;
    }

    /**
     * Define o template para ser usado no single de $cpt['name']
     */
    public function set_custom_single_template($single_template) {
        global $post;
        if (is_singular($this->cpt['name'])) {
            $single_template = $this->cpt['single_template'];
        }
        return $single_template;
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
        $option  = $this->admin['settings']['option_name'];
        $id      = $this->admin['fields']['id']['id'];
        $name    = $option . '[' . $id . ']';
        $options = get_option($option);
        $this->render_input($id, $name, $options[$id]);
    }

    /**
     * Client Secret
     */
    public function client_secret() {
        $option  = $this->admin['settings']['option_name'];
        $id      = $this->admin['fields']['secret']['id'];
        $name    = $option . '[' . $id . ']';
        $options = get_option($option);
        $this->render_input($id, $name, $options[$id]);
    }

    /**
     * Client UID
     */
    public function client_uid() {
        $option  = $this->admin['settings']['option_name'];
        $id      = $this->admin['fields']['uid']['id'];
        $name    = $option . '[' . $id . ']';
        $options = get_option($option);
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
        $url = get_post_type_archive_link($this->cpt['name']);
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
        settings_fields($this->admin['settings']['option_group']);
        do_settings_sections($this->admin['settings']['option_group']);
        ?>
                <input type="submit" id="submit" class="btn btn-primary" value="Salvar alterações" />
                <input type="button" id="get_token" class="btn btn-secondary" name="get_token" value="Gerar token" />
                </fieldset>
            </form>
        </div>
    <?php
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