<?php

function hgodbee_modal_template_save() {
    ?>
    <div class="modal fade" id="templateSave" data-keyboard="false" tabindex="-1" role="dialog"
        aria-labelledby="templateSaveLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="templateSaveLabel">Salvar template</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="salvaTemplateForm">
                        <div class="form-group">
                            <label for="nomeTemplate"><strong>Nome do template</strong></label>
                            <input type="text" class="form-control" id="nomeTemplate" required>
                        </div>
                        <div class="form-group">
                            <label for="descricaoTemplate"><strong>Descrição</strong></label>
                            <textarea class="form-control" id="descricaoTemplate" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-outline-success" id="salvarTemplateBTN">Salvar</button>
                </div>
            </div>
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