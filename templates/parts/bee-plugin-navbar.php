<?php
/**
 * Navbar Functions
 */

 /**
  * Define a navbar principal
  *
  * @return void
  */
function hgobee_navbar() {
    $config = include dirname( plugin_dir_path( __FILE__ ), 2 ) . '/config/config.php';
    $templates_url = esc_url( get_post_type_archive_link( $config['prefix'] . 'templates' ) );
    ?>
    <div id="bee-plugin-navigation-button-group" class="ui vertical icon buttons hgodbee-navbar" role="group" aria-label="buton group">
        <a type="button" href="<?php echo $templates_url; ?>" class="ui teal button" data-tooltip="Templates" data-position="right center"><span class="dashicons dashicons-archive"></span></a>
        <a type="button" class="ui blue button" data-tooltip="Download" data-position="right center"><span class="dashicons dashicons-download"></span></a>
        <a type="button" class="ui red button" data-tooltip="Deletar" data-position="right center"><span class="dashicons dashicons-trash"></span></a>
    </div>
<?php
}

/**
 * Define o botão de editar na visualização do HTML
 *
 * @return void
 */
function hgodbee_edit_button(){
    $edit_url = esc_url(add_query_arg( 'action', 'edit', get_permalink() ));
    ?>
    <a href="<?php echo $edit_url; ?>" class="ui right labeled yellow icon button hgodbee-navbar _edit-button">
        <i class="right arrow icon"></i>
        Editar
    </a>
<?php
}