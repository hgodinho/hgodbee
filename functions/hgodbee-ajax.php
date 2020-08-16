<?php
//HGodBee::hb_var_dump(('im here'), __CLASS__, __METHOD__, __LINE__, true);
/**
 * AJAX FUNCTIONS!
 */
/**
 * Retrieves the Bee Token
 *
 * You must have a developer account at https://developers.beefree.io/
 *
 * @return void
 */
function hgodbee_token() {
	$nonce = check_ajax_referer(HB_PREFIX . 'admin', 'nonce');
	global $wpdb;
	$options       = get_option(HB_PREFIX . 'settings_name');
	$client_id     = $options[HB_PREFIX . 'id'];
	$client_secret = $options[HB_PREFIX . 'secret'];
	$beeplugin     = new BeeFree($client_id, $client_secret);
	$token         = $beeplugin->getCredentials();

	if (isset($token->access_token)) {
		$token_saved = update_option(HB_PREFIX . 'token', $token);

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

/**
 * Salva template
 */
function hgodbee_save_template() {
	$nonce = check_ajax_referer(HB_PREFIX . 'save_as_template', 'nonce');
	global $wpdb;
	$json_template        = $_POST['json'];
	$template_name        = $_POST['name'];
	$template_description = $_POST['dsc'];
	$cpt                  = HB_PREFIX . 'templates';
	if (post_exists($template_name)) {
		$page_id         = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '" . $template_name . "' AND post_type = '" . $cpt . "'");
		$template_update = array(
			'ID'           => $page_id,
			'post_content' => $json_template,
			'post_excerpt' => $template_description,
		);
		wp_update_post($template_update);
		hgodbee_beeplugin_notification('Template atulizado com sucesso', $template_name, 'teal');
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
			hgodbee_beeplugin_notification('Template criado com sucesso', $template_name, 'green');
		} else {
			hgodbee_beeplugin_notification('Você não tem permissão para fazer isso.', $template_name, 'orange');
		}
	}
	delete_transient('emakbee_autosave');
	delete_transient('emakbee_user');
	wp_die();
}

/**
 * Salva template
 *
 * @return void
 */
function hgodbee_save() {
	$nonce = check_ajax_referer(HB_PREFIX . 'save', 'nonce');

	if (current_user_can('edit_posts')) {
		global $wpdb;
		$json_template = $_POST['json'];
		$html_file     = $_POST['html'];
		$template_name = $_POST['name'];
		$template_ID   = $_POST['id'];
		$categories    = $_POST['categories'];
		$tags          = $_POST['tags'];
		$cpt           = HB_PREFIX . 'templates';
		$url           = wp_get_referer();
		$post_id       = url_to_postid($url);

		if ($post_id == $template_ID) {
			/**
			 * ATUALIZA O TEMPLATE
			 */
			$template_update = array(
				'ID'           => $template_ID,
				'post_title'   => $template_name,
				'post_content' => $json_template,
				//'post_excerpt' => $template_description,
			);
			$saved = wp_update_post($template_update); // return (int|WP_Error) The post ID on success. The value 0 or WP_Error on failure.

			/**
			 * Salva o HTML no meta field
			 */
			$saved_html = update_post_meta($saved, HB_PREFIX . 'saved_html', $html_file); //return(int|bool) The new meta field ID if a field with the given key didn't exist and was therefore added, true on successful update, false on failure.

			/**
			 * Adiciona e relaciona as categorias ao post que acabamos de adicionar
			 *
			 * Aqui primeiro criamos um array para armazernar as ids dos termos
			 * depois interagimos no loop verificando se elas já existem no banco
			 * de dados, se não existirem, adicionamos e colocamos a ID dos termos
			 * no array criado anteriormente. Se o termo já existe, somente iserimos
			 * a ID do termo no array $terms_id. Por fim relacionamos o o array com
			 * o template criado anteriormente.
			 */
			$terms_id = array();
			foreach ($categories as $category) {
				if (!term_exists($category, HB_PREFIX . 'tax')) {
					$term_added = wp_insert_term($category, HB_PREFIX . 'tax'); // return (array|WP_Error) An array containing the term_id and term_taxonomy_id, WP_Error otherwise.
					array_push($terms_id, $term_added['term_id']);
				} else {
					$term = get_term_by('name', $category, HB_PREFIX . 'tax');
					array_push($terms_id, $term->term_id);
				}
			}
			$term_related = wp_set_object_terms($saved, $terms_id, HB_PREFIX . 'tax', false); // return (array|WP_Error) Term taxonomy IDs of the affected terms or WP_Error on failure.

			/**
			 * Adiciona e relaciona as tags ao post que acabamos de adicionar
			 *
			 * Aqui primeiro criamos um array para armazernar as ids dos termos
			 * depois interagimos no loop verificando se elas já existem no banco
			 * de dados, se não existirem, adicionamos e colocamos a ID dos termos
			 * no array criado anteriormente. Se o termo já existe, somente iserimos
			 * a ID do termo no array $tags_id. Por fim relacionamos o o array com
			 * o template criado anteriormente.
			 */
			$tags_id = array();
			foreach ($tags as $tag) {
				if (!term_exists($tag['value'], HB_PREFIX . 'tag')) {
					$tag_added = wp_insert_term($tag['value'], HB_PREFIX . 'tag'); // return (array|WP_Error) An array containing the term_id and term_taxonomy_id, WP_Error otherwise.
					array_push($tags_id, $tag_added['term_id']);
				} else {
					$tag = get_term_by('name', $tag['value'], HB_PREFIX . 'tag');
					array_push($tags_id, $tag->term_id);
				}
			}
			$tag_related = wp_set_object_terms($saved, $tags_id, HB_PREFIX . 'tag', false); // return (array|WP_Error) Term taxonomy IDs of the affected terms or WP_Error on failure.

			/**
			 * Resposta Ajax
			 */
			$post     = get_post($saved);
			$response = array(
				'success' => 1, // template atualizado
				'message' => 'Template atualizado: ' . $template_name,
			);
			print json_encode($response);

		} else {
			/**
			 * INSERE O TEMPLATE
			 */
			$saved = wp_insert_post(
				array(
					'post_content' => $json_template,
					'post_title'   => $template_name,
					//'post_excerpt' => $template_description,
					'post_status'  => 'publish',
					'post_type'    => $cpt,
				)
			);

			/**
			 * Salva o HTML no metafield
			 */
			$saved_html = update_post_meta($saved, HB_PREFIX . 'saved_html', $html_file);

			/**
			 * Adiciona e relaciona as categorias ao post que acabamos de adicionar
			 *
			 * Aqui primeiro criamos um array para armazernar as ids dos termos
			 * depois interagimos no loop verificando se elas já existem no banco
			 * de dados, se não existirem, adicionamos e colocamos a ID dos termos
			 * no array criado anteriormente. Se o termo já existe, somente iserimos
			 * a ID do termo no array $terms_id. Por fim relacionamos o o array com
			 * o template criado anteriormente.
			 */
			$terms_id = array();
			foreach ($categories as $category) {
				if (!term_exists($category, HB_PREFIX . 'tax')) {
					$term_added = wp_insert_term($category, HB_PREFIX . 'tax'); // return (array|WP_Error) An array containing the term_id and term_taxonomy_id, WP_Error otherwise.
					array_push($terms_id, $term_added['term_id']);
				} else {
					$term = get_term_by('name', $category, HB_PREFIX . 'tax');
					array_push($terms_id, $term->term_id);
				}
			}
			$term_related = wp_set_object_terms($saved, $terms_id, HB_PREFIX . 'tax', false); // return (array|WP_Error) Term taxonomy IDs of the affected terms or WP_Error on failure.

			/**
			 * Adiciona e relaciona as tags ao post que acabamos de adicionar
			 *
			 * Aqui primeiro criamos um array para armazernar as ids dos termos
			 * depois interagimos no loop verificando se elas já existem no banco
			 * de dados, se não existirem, adicionamos e colocamos a ID dos termos
			 * no array criado anteriormente. Se o termo já existe, somente iserimos
			 * a ID do termo no array $tags_id. Por fim relacionamos o o array com
			 * o template criado anteriormente.
			 */
			$tags_id = array();
			foreach ($tags as $tag) {
				if (!term_exists($tag['value'], HB_PREFIX . 'tag')) {
					$tag_added = wp_insert_term($tag['value'], HB_PREFIX . 'tag'); // return (array|WP_Error) An array containing the term_id and term_taxonomy_id, WP_Error otherwise.
					array_push($tags_id, $tag_added['term_id']);
				} else {
					$tag = get_term_by('name', $tag['value'], HB_PREFIX . 'tag');
					array_push($tags_id, $tag->term_id);
				}
			}
			$tag_related = wp_set_object_terms($saved, $tags_id, HB_PREFIX . 'tag', false); // return (array|WP_Error) Term taxonomy IDs of the affected terms or WP_Error on failure.

			/**
			 * Resposta Ajax
			 */
			$post     = get_post($saved);
			$response = array(
				'success' => 2, // template criado
				'message' => $template_name,
			);
			print json_encode($response);
		}
	} else {
		/**
		 * Resposta Ajax
		 */
		$post     = get_post($saved);
		$response = array(
			'success' => 0, // erro
			'message' => 'Você não tem permissão para fazer isso.',
		);
		print json_encode($response);
	}
	delete_transient('emakbee_autosave');
	delete_transient('emakbee_user');
	wp_die();
}

/**
 * Salva novo template
 *
 * @return void
 */
function hgodbee_save_new() {
	$nonce = check_ajax_referer(HB_PREFIX . 'save', 'nonce');

	if (current_user_can('edit_posts')) {
		global $wpdb;
		$json_template = $_POST['json'];
		$html_file     = $_POST['html'];
		$template_name = $_POST['name'];
		$categories    = $_POST['categories'];
		$tags          = $_POST['tags'];
		$cpt           = HB_PREFIX . 'templates';

		/**
		 * INSERE O TEMPLATE
		 */
		$saved = wp_insert_post(
			array(
				'post_content' => $json_template,
				'post_title'   => $template_name,
				'post_status'  => 'publish',
				'post_type'    => $cpt,
			)
		);
		HGodBee::hb_log($saved, 'post', __CLASS__, __METHOD__, __LINE__);

		/**
		 * Salva o HTML no metafield
		 */
		$saved_html = update_post_meta($saved, HB_PREFIX . 'saved_html', $html_file);

		/**
		 * Adiciona e relaciona as categorias ao post que acabamos de adicionar
		 *
		 * Aqui primeiro criamos um array para armazernar as ids dos termos
		 * depois interagimos no loop verificando se elas já existem no banco
		 * de dados, se não existirem, adicionamos e colocamos a ID dos termos
		 * no array criado anteriormente. Se o termo já existe, somente iserimos
		 * a ID do termo no array $terms_id. Por fim relacionamos o o array com
		 * o template criado anteriormente.
		 */
		$terms_id = array();
		foreach ($categories as $category) {
			if (!term_exists($category, HB_PREFIX . 'tax')) {
				$term_added = wp_insert_term($category, HB_PREFIX . 'tax'); // return (array|WP_Error) An array containing the term_id and term_taxonomy_id, WP_Error otherwise.
				array_push($terms_id, $term_added['term_id']);
			} else {
				$term = get_term_by('name', $category, HB_PREFIX . 'tax');
				array_push($terms_id, $term->term_id);
			}
		}
		$term_related = wp_set_object_terms($saved, $terms_id, HB_PREFIX . 'tax', false); // return (array|WP_Error) Term taxonomy IDs of the affected terms or WP_Error on failure.

		/**
		 * Adiciona e relaciona as tags ao post que acabamos de adicionar
		 *
		 * Aqui primeiro criamos um array para armazernar as ids dos termos
		 * depois interagimos no loop verificando se elas já existem no banco
		 * de dados, se não existirem, adicionamos e colocamos a ID dos termos
		 * no array criado anteriormente. Se o termo já existe, somente iserimos
		 * a ID do termo no array $tags_id. Por fim relacionamos o o array com
		 * o template criado anteriormente.
		 */
		$tags_id = array();
		foreach ($tags as $tag) {
			if (!term_exists($tag['value'], HB_PREFIX . 'tag')) {
				$tag_added = wp_insert_term($tag['value'], HB_PREFIX . 'tag'); // return (array|WP_Error) An array containing the term_id and term_taxonomy_id, WP_Error otherwise.
				array_push($tags_id, $tag_added['term_id']);
			} else {
				$tag = get_term_by('name', $tag['value'], HB_PREFIX . 'tag');
				array_push($tags_id, $tag->term_id);
			}
		}
		$tag_related = wp_set_object_terms($saved, $tags_id, HB_PREFIX . 'tag', false); // return (array|WP_Error) Term taxonomy IDs of the affected terms or WP_Error on failure.

		/**
		 * Resposta Ajax
		 */
		$post     = get_post($saved);
		$response = array(
			'success' => 1,
			'message' => $post->post_name,
		);
		print json_encode($response);

	} else {
		/**
		 * Resposta Ajax
		 */
		$post     = get_post($saved);
		$response = array(
			'success' => 0,
			'message' => 'Você não tem permissão para fazer isso.',
		);
		print json_encode($response);
	}
	delete_transient('emakbee_autosave');
	delete_transient('emakbee_user');
	wp_die();
}

/**
 * Delete template
 *
 * @return void
 */
function hgodbee_delete() {
	$nonce       = check_ajax_referer(HB_PREFIX . 'delete', 'nonce');
	$template_ID = $_POST['id'];

	$response = array(
		'success' => 0,
		'message' => 'Não deletado.',
	);

	$delete = wp_delete_post($template_ID);

	if ($template_ID == $delete->ID) {
		$response = array(
			'success' => 1,
			'message' => $delete->post_title,
		);
		print json_encode($response);
	} else {
		print json_encode($response);
	}
	wp_die();
}