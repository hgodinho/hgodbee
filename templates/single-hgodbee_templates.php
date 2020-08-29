<?php
/**
 *
 */
wp_head();

if (is_user_logged_in()) {
	$config         = include dirname(plugin_dir_path(__FILE__)) . '/config/config.php';
	$init_beeplugin = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-starter.php';
	$sidebar        = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-sidebar.php';
	$container      = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-container.php';
	$modals         = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-modals.php';
	$navbar         = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-navbar.php';
	$dimmer         = include plugin_dir_path(__FILE__) . 'parts/bee-plugin-dimmer.php';

	/**
	 * Prepara o array com as categorias gerais do post.
	 * São usadas para copiar seus valores para a categoria do template.
	 */
	$args = array(
		'taxonomy' => 'category',
		'orderby'  => 'name',
		'order'    => 'ASC',
	);
	$categorias       = get_terms($args); // Query com todas as categorias para copiar para a categoria do template.
	$categorias_posts = array();
	foreach ($categorias as $categoria) {
		$categoria_post = array(
			'id'   => $categoria->term_id,
			'name' => $categoria->name,
			'slug' => $categoria->slug,
		);
		array_push($categorias_posts, $categoria_post);
	}

	/**
	 * Prepara o array para criar a sidebar de paleta de cores
	 */
	$terms_args = array(
		'taxonomy' => HB_PREFIX . 'tax',
		'orderby'  => 'name',
		'order'    => 'ASC',
	);
	$all_template_terms_query = get_terms($terms_args);
	$all_template_terms       = array();
	foreach ($all_template_terms_query as $result) {
		$template_term_color = get_term_meta($result->term_id, 'colors');
		$template_term       = array(
			'id'     => $result->term_id,
			'name'   => $result->name,
			'slug'   => $result->slug,
			'colors' => $template_term_color,
		);
		array_push($all_template_terms, $template_term);
	}

	/**
	 * Prepara o array com os termos usados no template.
	 */
	$used_terms     = get_the_terms(get_the_ID(), HB_PREFIX . 'tax'); // Object array of terms in use.
	$template_terms = array(); // Array of template terms.
	foreach ($used_terms as $used_term) {
		$colors        = get_term_meta($used_term->term_id, 'colors');
		$template_term = array(
			'id'     => $used_term->term_id,
			'name'   => $used_term->name,
			'slug'   => $used_term->slug,
			'colors' => $colors,
		);
		array_push($template_terms, $template_term);
	}
	//HGodBee::hb_var_dump($template_terms, __CLASS__, __METHOD__, __LINE__, true);

	/**
	 * Prepara a lista de tags separadas por vírgulas contidas no template.
	 */
	$tags      = get_the_terms(get_the_ID(), HB_PREFIX . 'tag'); // Object array of tags in use.
	$tags_name = array(); // Array of names of the tags in use.
	foreach ($tags as $tag) {
		array_push($tags_name, $tag->name);
	}
	$tags_list = implode(', ', $tags_name); // Lista de tags separadas por vírgula.

	hgodbee_sidebar($used_terms, $all_template_terms, $template_terms);
	?>
<div class="pusher">
    <?php
hgodbee_plugin_container();
	hgodbee_dimmers();
	hgobee_navbar();
	hgodbee_beeplugin_notification_area();
	hgodbee_modal_template_save($categorias_posts, $template_terms, $tags_list);
	hgodbee_modal_template_delete();
	hgodbee_modal_template_download();
	//hgodbee_modal_send_test();

	$hgodbee_action = get_query_var('action');
	$client         = get_option($config['prefix'] . 'settings_name');
	$uid            = $client['hgodbee_uid'];
	$id             = $client['hgodbee_id'];
	$secret         = $client['hgodbee_secret'];
	$beeplugin      = new BeeFree($id, $secret);
	$token          = $beeplugin->getCredentials();
	$tokenJSON      = json_encode($token);
	$html           = get_post_meta(get_the_ID(), $config['prefix'] . 'saved_html');

	if ('comece-do-zero' === $post->post_name) {
		$template = get_the_content();
		hgodbee_plugin_starter($tokenJSON, $uid, $template);
	} else {
		if (!empty($hgodbee_action)) {
			if ('edit' === $hgodbee_action) {
				$template = get_the_content();
				hgodbee_plugin_starter($tokenJSON, $uid, $template);
			}
			if ('view' === $hgodbee_action) {
				if (isset($html[0])) {
					echo '<div class="ui raised segment html-wraper">';
					echo '<iframe class="html-code" srcdoc="' . esc_html($html[0]) . '">';
					echo '</iframe>';
					echo '</div>';
				}
			}
		} else {
			if (isset($html[0])) {
				echo '<div class="ui raised segment html-wraper">';
				echo '<iframe class="html-code" srcdoc="' . esc_html($html[0]) . '">';
				echo '</iframe>';
				echo '</div>';
			} else {
				echo 'ops, algo deu errado.';
			}
		}
		?>
</div>
<?php
}

} else {
	global $wp_query;
	$wp_query->set_404();
	status_header(404);
	get_template_part(404);exit();
}
wp_footer();