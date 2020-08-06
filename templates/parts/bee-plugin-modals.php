<?php


function hgodbee_modal_template_save() {
    $config = include dirname(plugin_dir_path( __FILE__ ), 2) . '/config/config.php';
    global $post;
    if ('comece-do-zero' === $post->post_name ) {
        $title = '';
        $templateID = '';
    } else {
        $title = get_the_title();
        $templateID = get_the_ID();
    }
    ?>
<div class="ui tiny modal" id="templateSave" tabindex="-1" role="dialog" aria-labelledby="templateSaveLabel"
    aria-hidden="true">
    <i class="close icon"></i>
    <div class="content">
        <h2 class="header" id="templateSaveLabel">Salvar template</h2>
    </div>
    <div class="content">
        <form id="salvaTemplateForm" class="ui form">
            <input type="hidden" id="templateID" name="templateID" value="<?php echo $templateID ?>">

            <div class="field">
                <label for="nomeTemplate"><strong>Nome do template</strong></label>
                <input type="text" class="form-control" name="nomeTemplate" id="nomeTemplate" value="<?php echo $title?>" required>
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
                <?php
                    $used_terms = get_the_terms( $post->ID, $config['prefix'] . 'tax' );
                    $terms_in_use = array();
                    foreach ( $used_terms as $used_term ){
                        array_push($terms_in_use, $used_term->name );
                    }
                ?>
                <select multiple id="categoriasTemplate" class="ui search dropdown"
                    data-categories="<?php print_r($terms_in_use); ?>">
                    <option value="">Selecione categorias</options>
                        <?php
                    foreach ( $terms->get_terms() as $term ) {
                        if ( in_array( $term->name, $terms_in_use ) ){
                            echo '<option value="' . $term->name . '" selected>' . $term->name . '</options>';
                        } else {
                            echo '<option value="' . $term->name . '">' . $term->name . '</options>';   
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="field">
                <?php
                $tags_in_use = array();
                $tags       = wp_get_post_terms(get_the_ID(), $config['prefix'] . 'tag');
                foreach ( $tags as $tag ) {
                    array_push($tags_in_use, $tag->name);
                }
                $tags_list = implode(', ', $tags_in_use);
                ?>
                <label for="tagsTemplate"><strong>Tags</strong></label>
                <input type="text" class="form-control" name="tagsTemplate" id="tagsTemplate" value="<?php echo $tags_list; ?> " required>
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