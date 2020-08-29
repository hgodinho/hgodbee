<?php

function hgodbee_sidebar( $used_terms = '', $all_template_terms, $template_terms ) {
	?>
<div class="ui inverted vertical menu wide sidebar overlay">
	<div class="content">
		<div class="ui inverted segment">
			<div class="paleta-header _margin-top-20-100">
				<h3 class="ui inverted header">Cores</h3>
			</div>
			<p>
				<?php
				if ( ! empty( $used_terms ) ) {
						hgodbee_categoria_input( $all_template_terms, $template_terms );
					?>
				<div class="accordion-wraper _margin-top-10-100" data-terms='<?php echo json_encode($template_terms); ?>'>
				</div>
			</p>
				<?php
			} else {
					echo 'Selecione as categorias.';
			}
			?>
		</div>
	</div>
	<div class="huge ui green button save-colors">Salvar</div>
</div>

	<?php
}

function hgodbee_accordion( $used_term ) {
	// HGodBee::hb_var_dump($used_term, __CLASS__, __METHOD__, __LINE__, false);
	?>
<button class="accordion"><?php echo $used_term->name; ?></button>
<div class="panel">
	<p></p>
</div>
	<?php
}

function hgodbee_categoria_input( $all_template_terms, $template_terms ) {
	?>
	<form class="ui inverted form">

		<div class="field colors hide">

			<label>Paletas</label>
			<select multiple id="categoriasColor" class="ui search dropdown forcolors">
				<option value="">Selecione a paleta</option>
					<?php
					
					foreach ( $template_terms as $term ) {
						echo '<option value="' . 
								$term['slug'] . 
								'" data-colors=\'' . 
								json_encode($term['colors']) .
								'\' class="' . 
								$term['slug'] . 
								'" selected>' . 
								$term['name'] . 
							'</option>';
					}
					
					$categorias_clean = array_diff_assoc($all_template_terms, $template_terms);
					foreach ( $categorias_clean as $categoria ) {
						echo '<option value="' . 
								$categoria['slug'] . 
								'" data-colors=\'' .
								json_encode($categoria['colors']) .
								'\' class="' . 
								$categoria['slug'] .
								'">' . 
								$categoria['name'] . 
							'</option>';
					}
					
					?>
			</select>
		</div>

	</form>
	<?php
}
