var input_tags;

(function ($) {
    $('.dimmer.salvando').hide();

    $(document).ready(function () {
        console.log(hgodbee_interface);
        feather.replace();

        $('#categoriasTemplate').dropdown({
            allowAdditions: true,
            cleareable: true,
            action: 'activate',
        });

        $('.dimmer.card-dimmer').hide();

        /**
         * dimmer enquanto o ajax executa.
         */
        $(document)
            .ajaxStart(function () {
                $('.dimmer.salvando').show();
            })
            .ajaxStop(function () {
                $('.dimmer.salvando').hide();
            });

        /**
         * funções de acordo com o parâmetro da url action
         */
        var action = getUrlParameter('action');
        console.log(action);
        if (action == 'edit' || typeof action === 'undefined') {
            input_tags = $('input[name=tagsTemplate]')
                .tagify()
                .on('add', function (e, tagName) {})
                .on('invalid', function (e, tagName) {});
        }
        if (action == 'view' || action == null) {
            $('.dimmer.carregando').hide();
            $('.field.colors').toggleClass('hide');
        }

        /**
         * Função do botão delete no arquivo de templates
         * 
         * {AJAX}
         */
        $('.template-delete').unbind('click').click(function () {
            event.preventDefault();
            var id = $(this).attr('id');
            var page = $(this).attr('data-page');
            console.log(page);
            $('#templateDelete')
                .modal({
                    inverted: false,
                })
                .modal('show');
            $('#deletarTemplateBTN').unbind('click').click(function () {
                event.preventDefault();

                $('#templateDelete')
                    .modal({
                        inverted: false,
                    })
                    .modal('hide');

                var columnCard = $('.templates.template-' + id);
                var card = $(columnCard).children('.card');
                card.dimmer('show');

                var data = {
                    action: 'hgodbee_delete',
                    id: id,
                    nonce: hgodbee_interface.nonce_delete,
                };

                var settings = {
                    url: ajaxURL,
                    data: data,
                    success: function (response) {
                        response = JSON.parse(response);
                        if (1 == response.success) {
                            $('body').toast({
                                position: 'top left _margin-top-3-100',
                                message: '<strong>Template deletado:</strong> ' +
                                    response.message,
                                displayTime: 5000,
                                class: 'inverted orange',
                                showProgress: 'bottom',
                            });
                        }
                        if (response.success == 1) {
                            columnCard.fadeOut(function () {
                                card.dimmer('hide');
                                columnCard.remove();
                            });
                        }
                        if ('single' === page) {
                            window.location.replace(hgodbee_interface.archive);
                        }
                    },
                    global: false,
                };

                $.post(settings);
            });
        });

        /**
         * Botão Estrela Salva como template
         * 
         * {AJAX}
         */
        $('.icon.star.template').unbind('click').click(function () {
            event.preventDefault();
            var starButton = $(this);

            $(starButton).toggleClass('active');
            if ($(starButton).hasClass('active')) {
                var active = 1;
            } else {
                active = 0;
            }
            var data = {
                action: 'hgodbee_save_template_star_button',
                id: $(this).data('id'),
                value: active,
                nonce: hgodbee_interface.nonce_star,
            };

            $.post(hgodbee_interface.ajax_url, data, function (response) {
                response = JSON.parse(response);
                if (response.success == 0) {
                    $('body').toast({
                        // erro
                        position: 'top left _margin-top-3-100',
                        message: response.msg,
                        displayTime: 5000,
                        class: 'inverted red',
                        showProgress: 'bottom',
                    });
                    $(starButton).toggleClass('active');
                }
                if (response.success == 1) {
                    // template atualizado
                    $('body').toast({
                        position: 'top left _margin-top-3-100',
                        message: response.msg,
                        displayTime: 5000,
                        class: 'inverted green',
                        showProgress: 'bottom',
                    });
                }
                if (response.success == 2) {
                    // template marcado como favorito
                    $('body').toast({
                        position: 'top left _margin-top-3-100',
                        message: response.msg,
                        displayTime: 5000,
                        class: 'inverted green',
                        showProgress: 'bottom',
                    });
                    //$(starButton).toggleClass('active');
                }
            }).done(function () {
                $('#templateSave').modal('hide');
            });
        });

        /**
         * Função do botão de download no single
         */
        $('.download').unbind('click').click(function () {
            event.preventDefault();
            $('#templateDownload')
                .modal({
                    inverted: false,
                })
                .modal('show');
            BeeApp.singleton.editor.plugin.send();
        });
    });
})(jQuery);