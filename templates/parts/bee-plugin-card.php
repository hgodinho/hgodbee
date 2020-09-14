<?php
/**
 * Bee plugin Card
 *
 * Para uso no arquivo de mailings
 *
 * @package hgodbee
 */

$dimmer = include plugin_dir_path( __FILE__ ) . 'bee-plugin-dimmer.php';

/**
 * Hgodbee_plugin_card
 *
 * @param array $taxs Taxonomias.
 * @param array $tags Tags.
 * @return void
 */
function hgodbee_plugin_card( $taxs, $tags ) {
	global $post;
	$last_id = get_post_meta( get_the_ID(), '_edit_last', true );
	if ( empty( $last_id ) ) {
		$autor = get_the_author_meta( 'user_login' );
	} else {
		$autor = get_the_modified_author();
	}
	?>
	<div class="column templates template-<?php echo esc_attr( get_the_ID() ); ?> ">
		<div class="ui teal fluid inverted card">
			<?php hgodbee_dimmer_carregando( 'card-dimmer' ); ?>
			<div class="extra content">
				<?php
				$icon_size   = '1.2em';
				$is_template = get_post_meta( get_the_ID(), HB_PREFIX . 'is_template', true );
				$active      = '';
				if ( $is_template ) {
					$active = 'active';
				}
				?>
				<i class="right floated icon star template <?php echo esc_attr( $active ); ?>" data-id="<?php echo esc_attr( get_the_ID() ); ?>"
					data-feather="star" width="<?php echo esc_attr( $icon_size ); ?>" height="<?php echo esc_attr( $icon_size ); ?>"></i>
				<div class="header"><?php the_title(); ?></div>
			</div>

			<div class="content">
				<?php
				if ( 'comece-do-zero' !== $post->post_name ) {
						$icon_size = '0.9em';
					?>
				<div class="meta">
					<span class="_autor"><i data-feather="user" width="<?php echo esc_attr( $icon_size ); ?>"
							height="<?php echo esc_attr( $icon_size ); ?>"></i>
						<?php echo esc_html( $autor ); ?></span><br>
					<span class="_data"><i data-feather="calendar" width="<?php echo esc_attr( $icon_size ); ?>"
							height="<?php echo esc_attr( $icon_size ); ?>"></i>
						<?php echo esc_html( get_the_modified_time( 'j/m/y', get_the_ID() ) ); ?></span> <span class="_time"><i
							data-feather="clock" width="<?php echo esc_attr( $icon_size ); ?>" height="<?php echo esc_attr( $icon_size ); ?>"></i>
						<?php echo esc_html( get_the_modified_time( 'H:i:s', get_the_ID() ) ); ?></span>
				</div> <!-- .meta -->
					<?php
				}
				if ( is_array( $taxs ) || is_object( $taxs ) ) {
					?>
					<div class="ui bulleted inverted list">
						<?php
						foreach ( $taxs as $tax ) {
							echo '<div class="item">' . esc_html( $tax ) . '</div>';
						}
						?>
					</div><!-- .horizontal .list -->
					<?php
				}
				if ( is_array( $tags ) || is_object( $tags ) ) {
					?>
					<div class="ui grey labels">
						<?php
						foreach ( $tags as $tag ) {
							echo '<span class="ui inverted label _margin-top-3">' . esc_html( $tag ) . '</span>';
						}
						?>
					</div><!-- .white .labels -->
					<?php
				}
				?>


			</div>
			<div class="ui bottom attached buttons" style="margin-top:10px;">
				<?php
				$edit_url = add_query_arg( 'action', 'edit', get_permalink() );
				$view_url = add_query_arg( 'action', 'view', get_permalink() );
				if ( 'comece-do-zero' === $post->post_name ) {
					echo '<a href="' . esc_url( $edit_url ) . '" class="ui teal button"><i class="icon" data-feather="edit-2" width="' . esc_attr( $icon_size ) . '" height="' . esc_attr( $icon_size ) . '"></i></a>';
				} else {
					$icon_size = '1.2em';
					?>
				<a href="<?php echo esc_url( $edit_url ); ?>" class="ui teal icon button"><i class="icon" data-feather="edit-2"
						width="<?php echo esc_attr( $icon_size ); ?>" height="<?php echo esc_attr( $icon_size ); ?>"></i></a>
				<a href="<?php echo esc_url( $view_url ); ?>" class="ui blue icon button"><i class="icon" data-feather="eye"
						width="<?php echo esc_attr( $icon_size ); ?>" height="<?php echo esc_attr( $icon_size ); ?>"></i></a>
				<a href="javascript:void(0)" id="<?php echo esc_html( get_the_ID() ); ?>" class="ui red icon button template-delete"><i
						data-feather="trash-2" width="<?php echo esc_attr( $icon_size ); ?>" height="<?php echo esc_attr( $icon_size ); ?>"></i></a>
					<?php
				}
				?>

			</div>
		</div>
	</div>
	<?php
}
