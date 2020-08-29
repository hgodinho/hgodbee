<?php
/**
 * Modals
 */

/**
 * Modal template save
 *
 * @param WP_Term_Object $categorias Categorias.
 * @param array          $terms_name Nome dos termos da categoria do template.
 * @param string         $tags_list Tags do template em lista separados por vírgula.
 */
function hgodbee_modal_template_save( $categorias, $template_terms, $tags_list ) {
	$config = include dirname( plugin_dir_path( __FILE__ ), 2 ) . '/config/config.php';
	global $post;
	if ( 'comece-do-zero' === $post->post_name ) {
		$title      = '';
		$templateID = '';
	} else {
		$title      = get_the_title();
		$templateID = get_the_ID();
	}
	?>
<div class="ui tiny inverted modal" id="templateSave" tabindex="-1" role="dialog" aria-labelledby="templateSaveLabel"
	aria-hidden="true">
	<i class="close icon"></i>
	<div class="content">
		<h2 class="ui inverted header" id="templateSaveLabel"><span class="dashicons dashicons-admin-post icon"></span>
			<div class="content">Salvar template</div>
		</h2>
	</div>
	<div class="content">
		<form id="salvaTemplateForm" class="ui inverted form">
			<input type="hidden" id="templateID" name="templateID" value="<?php echo $templateID; ?>">

			<div class="field">
				<label for="nomeTemplate"><strong>Nome do template</strong></label>
				<input type="text" class="form-control" name="nomeTemplate" id="nomeTemplate"
					value="<?php echo $title; ?>" required>
			</div>

			<div class="field">
				<?php hgodbee_categorias_select_input( $template_terms, $categorias, 'Categorias' ); ?>
				<?php //hgodbee_categorias_dropdown( $template_terms, $categorias, 'Categorias' ); ?>

			</div>

			<div class="field">
				<label for="tagsTemplate"><strong>Tags</strong></label>
				<input type="text" class="form-control" name="tagsTemplate" id="tagsTemplate"
					value="<?php echo $tags_list; ?> " required>
			</div>

		</form>
	</div>
	<div class="actions">
		<div id="salvarNovoBTN" class="ui left olive labeled icon approve button hgobee-modal _new_button">
			<i class="left plus circle icon"></i>
			Salvar novo
		</div>
		<div id="salvarTemplateBTN" class="ui approve teal button">Salvar</div>
		<div class="ui cancel basic red button">Continuar editando</div>
	</div>
</div>
	<?php
}

function hgodbee_categorias_select_input( $template_terms, $categorias, $label ) {
	?>
	<label><?php echo $label; ?></label>
	<select multiple id="categoriasTemplate" class="ui search dropdown">
		<option value="">Selecione as categorias</option>
		<?php
		foreach ( $template_terms as $option ) {
			echo '<option value="' . $option['slug'] . '" selected>' . $option['name'] . '</option>';
		}
		$diff = array_diff_assoc( $categorias, $template_terms );
		foreach ( $diff as $option ) {
			echo '<option value="' . $option['slug'] . '">' . $option['name'] . '</option>';
		}
		?>
	</select>
	<?php
}

function hgodbee_categorias_dropdown( $template_terms, $categorias, $label ) {
	$terms = array();
	foreach($template_terms as $term) {
		$term_redux = array(
			'name' => $term['name'],
			'slug' => $term['slug'],
		);
		array_push($terms, $term_redux);
	}
	?>
	<div id="categoriasTemplate" class="ui multiple selection dropdown">
		<?php echo '<input type="hidden" name="categorias[]" value=\'' . json_encode($terms) . '\'>'; ?>
		<i class="dropdown icon"></i>
		<div class="default text"><?php echo $label; ?></div>
		<div class="menu">
			<?php
			foreach ( $categorias as $option ) {
				echo '<div class="item" data-value="' . $option['slug'] . '">' . $option['name'] . '</div>';
			}
			?>
		</div>
	</div>
	<?php
}

function hgodbee_modal_template_download() {

	$title = get_the_title();
	$slug  = basename( get_permalink( get_the_ID() ) );

	?>
<div class="ui tiny inverted modal" id="templateDownload" tabindex="-1" role="dialog"
	aria-labelledby="templateDownloadLabel" aria-hidden="true">
	<i class="close icon"></i>
	<div class="content">
		<h2 class="ui inverted header" id="templateDownloadLabel"><span
				class="dashicons dashicons-download icon"></span>
			<div class="content">Baixar .zip</div>
		</h2>
	</div>
	<div class="content">
		<form id="dowloadTemplateForm" class="ui inverted form">
			<input type="hidden" name="slugArquivo" id="slugArquivo" value="<?php echo $slug; ?>">
			<div class="field">
				<label for="nomeArquivo"><strong>Nome do arquivo</strong></label>
				<input type="text" class="form-control" name="nomeArquivo" id="nomeArquivo"
					value="<?php echo $title; ?>" required>
			</div>

		</form>
	</div>
	<div class="actions">
		<div id="downloadBTN" class="ui approve teal button">Baixar</div>
		<div class="ui cancel basic red button">Continuar editando</div>
	</div>
</div>
	<?php
}

function hgodbee_modal_template_delete() {

	?>
<div class="ui tiny inverted modal" id="templateDelete" tabindex="-1" role="dialog"
	aria-labelledby="templateDeleteLabel" aria-hidden="true">
	<i class="close icon"></i>
	<div class="content">
		<h2 class="ui inverted header" id="templateSaveLabel"><span class="dashicons dashicons-trash icon"></span>
			<div class="content">Tem certeza que deseja deletar este template?</div>
		</h2>
	</div>
	<div class="actions">
		<div id="deletarTemplateBTN" class="ui basic green button">Sim, deletar!</div>
		<div class="ui cancel red button">Cancelar.</div>
	</div>
</div>
	<?php
}

function hgodbee_modal_send_test() {

	?>
<div class="modal fade" id="enviaTeste" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="enviaTeste"
	aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title">Envio de teste</h2>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="enviaTesteForm">
					<div class="form-group">
						<label for="enderecoEnvio"><strong>Para:</strong></label>
						<input type="text" class="form-control" id="enderecoEnvio" required>
					</div>
					<div class="form-group">
						<label for="assuntoEnvio"><strong>Assunto:</strong></label>
						<input type="text" class="form-control" id="assuntoEnvio" required>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-outline-success" id="enviaTesteBTN">Enviar</button>
			</div>
		</div>
	</div>
</div>
	<?php
}
