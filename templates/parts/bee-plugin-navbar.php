<?php
/**
 * Navbar Functions
 *
 * @package hgodbee
 */

/**
 * Define a navbar principal
 *
 * @return void
 */
function hgobee_navbar() {
	$templates_url = get_post_type_archive_link( HB_PREFIX . 'templates' );
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
				$action_classes = 'teal disabled';
			} else {
				$action_classes = 'olive';
			}
		}
		if ( 'view' === get_query_var( 'action' ) ) {
			$action_link    = add_query_arg( 'action', 'edit', get_permalink() );
			$action_icon    = 'edit-2';
			$action_tooltip = 'Editar';
			$action_classes = 'olive';
		}
	} else {
		$action_link    = add_query_arg( 'action', 'edit', get_permalink() );
		$action_icon    = 'edit-2';
		$action_tooltip = 'Editar';
		$action_classes = 'olive';
	}
	?>
<div id="bee-plugin-navigation-button-group" class="ui vertical icon buttons hgodbee-navbar" role="group"
	aria-label="buton group">
	<?php hgodbee_navbar_button( '', 'javascript:void(0)', 'teal cores', 'Cores', 'sliders' ); ?>
	<?php hgodbee_navbar_button( '', $templates_url, 'violet', 'Templates', 'archive' ); ?>
	<?php hgodbee_navbar_button( '', 'javascript:void(0)', 'pink download', 'Download', 'download' ); ?>
	<?php hgodbee_navbar_button( get_the_ID(), 'javascript:void(0)', 'red template-delete', 'Deletar', 'trash-2', $page ); ?>
</div>
	<?php
}

/**
 * Navbar
 *
 * @param string $id Id.
 * @param string $href Src.
 * @param string $class Classes.
 * @param string $tooltip Tooltip.
 * @param string $icon Icone.
 * @param string $page Página, pode ser ou single ou archive.
 * @return void
 */
function hgodbee_navbar_button( $id = '', $href = '', $class = '', $tooltip = '', $icon = '', $page = '' ) {
	$icon_size = '1.2em';
	if ( 'javascript:void(0)' !== $href ) {
		$href = esc_url( $href );
	} else {
		$href = esc_js( $href );
	}
	if ( '' !== $page ) {
		?>
		<a type="button" id="<?php echo esc_attr( $id ); ?>" href="<?php echo $href; ?>"
			class="ui inverted <?php echo esc_html( $class ); ?> button " data-tooltip="<?php echo esc_attr( $tooltip ); ?>"
			data-position="right center" data-page="<?php echo esc_attr( $page ); ?>"><i class="icon" data-feather="<?php echo esc_attr( $icon ); ?>"
				width="<?php echo esc_attr( $icon_size ); ?>" height="<?php echo esc_attr( $icon_size ); ?>"></i></a>
		<?php
	} else {
		?>
		<a type="button" id="<?php echo esc_attr( $id ); ?>" href="<?php echo $href; ?>"
			class="ui inverted <?php echo esc_html( $class ); ?> button " data-tooltip="<?php echo esc_attr( $tooltip ); ?>"
			data-position="right center"><i class="icon" data-feather="<?php echo esc_attr( $icon ); ?>"
				width="<?php echo esc_attr( $icon_size ); ?>" height="<?php echo esc_attr( $icon_size ); ?>"></i></a>
		<?php
	}
}
