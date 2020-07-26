<?php

function hgodbee_modal_template_save() {
    global $post;
    if ('comece-do-zero' === $post->post_name ) {
        $title = '';
    } else {
        $title = get_the_title(  );
    }
    ?>
<div class="ui tiny modal" id="templateSave" tabindex="-1" role="dialog" aria-labelledby="templateSaveLabel"
    aria-hidden="true">
    <i class="close icon"></i>
    <div class="modal-header">
        <h2 class="header" id="templateSaveLabel">Salvar template</h2>
    </div>
    <div class="content">
        <form id="salvaTemplateForm" class="ui form">
            <div class="field">
                <label for="nomeTemplate"><strong>Nome do template</strong></label>
                <input type="text" class="form-control" id="nomeTemplate" value="<?php echo $title?>" required>
            </div>

            <div class="field">
                <?php
                    $args = array(
                        'taxonomy' => 'category',
                        'orderby'  => 'name',
                        'order'    => 'ASC',
                    );
                    $terms = new WP_Term_Query($args);
                ?>
                <label>Categorias</label>
                <select multiple id="categoriasTemplate" class="ui search dropdown">
                    <option value="">Selecione categorias</options>
                    <?php
                    foreach ( $terms->get_terms() as $term ) {
                        echo '<option value="' . $term->name . '">' . $term->name . '</options>';
                    }
                    ?>
                </select>
            </div>

            <div class="field">
                <label for="descricaoTemplate"><strong>Descrição</strong></label>
                <textarea class="form-control" id="descricaoTemplate" required></textarea>
            </div>
        </form>
    </div>
    <div class="actions">
        <div id="salvarTemplateBTN" class="ui approve teal button">Salvar</div>
        <div class="ui cancel basic red button">Continuar editando</div>
    </div>
</div>
<?
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