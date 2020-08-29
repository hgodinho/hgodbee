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
	// $config        = include dirname(plugin_dir_path(__FILE__), 2) . '/config/config.php';
	$templates_url = esc_url( get_post_type_archive_link( $config['prefix'] . 'templates' ) );
	$action        = get_query_var( 'action' );
	$page;
	if ( is_singular( HB_PREFIX . 'templates' ) ) {
		$page = 'single';
	}
	if ( is_post_type_archive( HB_PREFIX . 'templates' ) ) {
		$page = 'archive';
	}
	if ( ! empty( $action ) ) {
		if ( 'edit' === get_query_var( 'action' ) ) {
			$action_link    = add_query_arg( 'action', 'view', get_permalink() );
			$action_icon    = 'eye';
			$action_tooltip = 'Visualizar';
			if ( 'comece-do-zero' === basename( get_the_permalink( get_the_ID() ) ) ) {
				$action_classes = 'ui teal inverted disabled button';
			} else {
				$action_classes = 'ui teal inverted button';
			}
		}
		if ( 'view' === get_query_var( 'action' ) ) {
			$action_link    = add_query_arg( 'action', 'edit', get_permalink() );
			$action_icon    = 'edit-2';
			$action_tooltip = 'Editar';
			$action_classes = 'ui teal inverted button';
		}
	} else {
		$action_link    = add_query_arg( 'action', 'edit', get_permalink() );
		$action_icon    = 'edit-2';
		$action_tooltip = 'Editar';
		$action_classes = 'ui teal inverted button';
	}
	$icon_size = '1.2em';
	?>
<div id="bee-plugin-navigation-button-group" class="ui vertical icon buttons hgodbee-navbar" role="group"
	aria-label="buton group">

	
	<a type="button" href="javascript:void(0)" class="ui pink inverted button cores" data-tooltip="Cores"
		data-position="right center"><i class="icon" data-feather="sliders" width="<?php echo $icon_size; ?>"
			height="<?php echo $icon_size; ?>"></i></a>

	<a type="button" href="<?php echo $templates_url; ?>" class="ui yellow inverted button" data-tooltip="Templates"
		data-position="right center"><i class="icon" data-feather="archive" width="<?php echo $icon_size; ?>"
			height="<?php echo $icon_size; ?>"></i></a>

	<a type="button" href="<?php echo $action_link; ?>" class="<?php echo $action_classes; ?>"
		data-tooltip="<?php echo $action_tooltip; ?>" data-position="right center"><i class="icon"
			data-feather="<?php echo $action_icon; ?>" width="<?php echo $icon_size; ?>"
			height="<?php echo $icon_size; ?>"></i></a>

	<a type="button" class="ui purple inverted button download" data-tooltip="Download" data-position="right center"><i
			class="icon" data-feather="download" width="<?php echo $icon_size; ?>"
			height="<?php echo $icon_size; ?>"></i></a>

	<a type="button" id="<?php echo get_the_ID(); ?>" class="ui red inverted button template-delete" data-tooltip="Deletar"
		data-position="right center" data-page="<?php echo $page; ?>"><i class="icon" data-feather="trash-2"
			width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>"></i></a>

</div>
	<?php
}

function hgodbee_navbar_button($href = '', $class = '', $tooltip = '', $icon = '' ) {
	$icon_size = '1.2em';
	?>
	<a type="button" href="<?php echo $href; ?>" class="ui inverted <?php echo esc_html($class); ?> button "
		data-tooltip="<?php echo esc_html($tooltip); ?>" data-position="right center"><i class="icon"
			data-feather="<?php echo esc_html($icon); ?>" width="<?php echo esc_html($icon_size); ?>"
			height="<?php echo esc_html($icon_size); ?>"></i></a>
	<?php
}

/**
 * Define o botão de editar na visualização do HTML
 *
 * @return void
 */
function hgodbee_edit_button() {
	$edit_url = esc_url( add_query_arg( 'action', 'edit', get_permalink() ) );
	?>
<a href="<?php echo $edit_url; ?>" class="ui right labeled yellow icon button hgodbee-navbar _edit-button">
	<i class="right arrow icon"></i>
	Editar
</a>
	<?php
}
