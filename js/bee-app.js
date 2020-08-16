/**
 * EmakBEE js
 *
 * @author hgodinho
 */

var token = hgodbee_object.token;
var ajaxURL = hgodbee_object.ajax_url;
var input_tags;

(function ($) {
    $('.dimmer.salvando').hide();

    $(document).ready(function () {
        feather.replace();
        $('.dimmer.card-dimmer').hide();

        $(document)
            .ajaxStart(function () {
                $('.dimmer.salvando').show();
            })
            .ajaxStop(function () {
                $('.dimmer.salvando').hide();
            });

        /**
         * Close notifications on notification-area
         */
        $(document).delegate('.message .close', 'click', function () {
            $(this).closest('.message').transition('fade');
        });

        var action = getUrlParameter('action');
        console.log(action);
        if (action == 'edit') {
            input_tags = $('input[name=tagsTemplate]')
                .tagify()
                .on('add', function (e, tagName) {})
                .on('invalid', function (e, tagName) {});
        }
        if (action == 'view' || action == null) {
            $('.dimmer.carregando').hide();
        }

        $('.cores').click(function () {
            $('.ui.accordion').accordion();
            $('.ui.sidebar').sidebar('setting', 'transition', 'overlay').sidebar('toggle');
        });

        /**
         * Accordion
         */
        var accordion = $('button.accordion');
        var i;
        for ( i = 0; accordion.length; i++ ) {
            accordion[i].addEventListener('click', function() {
                $(this).toggleClass('active');
                var panel = $(this).next();
                if(panel.css('maxHeight') !== '0px') {
                    panel.css('maxHeight', '0px');
                } else {
                    panel.css('maxHeight', panel[0].scrollHeight + 'px' );
                }
            });
        }

        /**
         * Função do botão delete no arquivo de templates
         */
        $('.delete').click(function () {
            event.preventDefault();
            var id = $(this).attr('id');
            var page = $(this).attr('data-page');
            $('#templateDelete')
                .modal({
                    inverted: false,
                })
                .modal('show');
            $('#deletarTemplateBTN').click(function () {
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
                    nonce: hgodbee_object.nonce_delete,
                };
                $.post(ajaxURL, data, function (response) {
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
                        window.location.replace(hgodbee_object.archive);
                    }
                }).done(function () {});
            });
        });

        $('.download').click(function () {
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

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ?
                true :
                decodeURIComponent(sParameterName[1]);
        }
    }
};

function getBeeConfig(user_or_account_id, container_id) {
    return {
        uid: '__' + user_or_account_id,
        container: container_id,
        autosave: 30,
        preventClose: true,
        language: 'pt-BR',
    };
}

function BeeApp(token, bee_config, beetemplates) {
    BeeApp.singleton = this;
    BeeApp.singleton.callbacks = {};
    var that = this;
    this.bee_config = bee_config;

    /* Use the leftside as a staging area to make transitions smoother.*/
    function offscreenElement(element) {
        if (element) {
            element.oldStyle = {
                position: element.style.position,
                left: element.style.left,
            };
            element.style.position = 'absolute';
            element.style.left = '-999999px';
        }
    }

    function onscreenElement(element) {
        if (element && element.style) {
            element.style.position = element.oldStyle.position;
            element.style.left = element.oldStyle.left;
        }
    }

    function BeeCallbacks(app, bee_config) {
        this.app = app;

        bee_config.onLoad = function () {
            app.onLoadEditor();
        };
        (bee_config.onAutoSave = function (jsonFile) {
            var data = {
                action: 'emakbee_autosave',
                json: jsonFile,
                nonce: emakbee_infos.nonce_autosave,
            };
            jQuery.post(ajaxURL, data, function (response) {
                console.log(response);
            });
        }),
        (bee_config.onSave = function (jsonFile, htmlFile) {
            jQuery('#templateSave')
                .modal({
                    inverted: false,
                })
                .modal('show');

            jQuery('select.dropdown').dropdown();

            jQuery('#salvarTemplateBTN').click(function () {
                event.preventDefault();
                var templateID = jQuery('input[name=templateID').val();
                var templateName = document.getElementById('nomeTemplate')
                    .value;
                var categories = jQuery('#categoriasTemplate').val();
                var tags = input_tags.data('tagify').value;
                if (templateName == '' || categories == '' || tags == '') {
                    alert(
                        'OPS! Parece que algum dos campos está vazio. Preencha todos os campos.'
                    );
                    return false;
                } else {
                    var data = {
                        action: 'hgodbee_save',
                        id: templateID,
                        name: templateName,
                        json: jsonFile,
                        html: htmlFile,
                        categories: categories,
                        tags: tags,
                        nonce: hgodbee_object.nonce_save,
                    };
                    jQuery
                        .post(ajaxURL, data, function (response) {
                            response = JSON.parse(response);
                            if (response.success == 0) {
                                jQuery('body').toast({
                                    // erro
                                    position: 'top left _margin-top-3-100',
                                    message: response.message,
                                    displayTime: 5000,
                                    class: 'inverted red',
                                    showProgress: 'bottom',
                                });
                            }
                            if (response.success == 1) {
                                // template atualizado
                                jQuery('body').toast({
                                    position: 'top left _margin-top-3-100',
                                    message: response.message,
                                    displayTime: 5000,
                                    class: 'inverted teal',
                                    showProgress: 'bottom',
                                });
                            }
                            if (response.success == 2) {
                                // template criado
                                jQuery('body').toast({
                                    position: 'top left _margin-top-3-100',
                                    message: 'Template criado: ' +
                                        response.message +
                                        '\nredirecionando...',
                                    displayTime: 5000,
                                    class: 'inverted olive',
                                    showProgress: 'bottom',
                                });
                                window.location.replace(
                                    hgodbee_object.archive +
                                    '/' +
                                    response.message +
                                    '?action=edit'
                                );
                            }
                        })
                        .done(function () {
                            jQuery('#templateSave').modal('hide');
                        });
                }
            });

            jQuery('#salvarNovoBTN').click(function () {
                event.preventDefault();
                var templateName = document.getElementById('nomeTemplate')
                    .value;
                var categories = jQuery('#categoriasTemplate').val();
                var tags = input_tags.data('tagify').value;
                if (templateName == '' || categories == '' || tags == '') {
                    alert(
                        'OPS! Parece que algum dos campos está vazio. Preencha todos os campos.'
                    );
                    return false;
                } else {
                    var data = {
                        action: 'hgodbee_save_new',
                        name: templateName,
                        json: jsonFile,
                        html: htmlFile,
                        categories: categories,
                        tags: tags,
                        nonce: hgodbee_object.nonce_save,
                    };
                    jQuery
                        .post(ajaxURL, data, function (response) {
                            response = JSON.parse(response);
                            if (response.success == 1) {
                                jQuery('body').toast({
                                    position: 'top left _margin-top-3-100',
                                    message: '<strong>Template criado:</strong> ' +
                                        response.message +
                                        '\n...redirecionando',
                                    displayTime: 5000,
                                    class: 'inverted green',
                                    showProgress: 'bottom',
                                });
                                window.location.replace(
                                    hgodbee_object.archive +
                                    '/' +
                                    response.message +
                                    '?action=edit'
                                );
                            } else {
                                jQuery('body').toast({
                                    position: 'top left _margin-top-3-100',
                                    message: response.message,
                                    displayTime: 5000,
                                    class: 'inverted red',
                                    showProgress: 'bottom',
                                });
                            }
                        })
                        .done(function () {
                            jQuery('#templateSave').modal('hide');
                        });
                }
            });
        }),
        (bee_config.onSaveAsTemplate = function (jsonFile) {
            jQuery('#templateSave').modal('show');
            jQuery('#salvarTemplateBTN').click(function () {
                event.preventDefault();
                var templateName = document.getElementById('nomeTemplate')
                    .value;
                var templateDescription = document.getElementById(
                    'descricaoTemplate'
                ).value;

                if (templateName == '' || templateDescription == '') {
                    alert('Os campos precisam ser preenchidos.');
                    return false;
                } else {
                    var data = {
                        action: 'hgodbee_save_template',
                        name: templateName,
                        dsc: templateDescription,
                        json: jsonFile,
                        nonce: hgodbee_object.nonce_save_as_template,
                    };
                    jQuery
                        .post(ajaxURL, data, function (response) {
                            //alert(response);
                            var notificationArea = document.getElementById(
                                'notification-area'
                            );
                            notificationArea.innerHTML = response;
                        })
                        .done(jQuery('#templateSave').modal('hide'));
                }
            });
        }),
        (bee_config.onSend = function (htmlFile) {
            jQuery('#downloadBTN').click(function () {
                event.preventDefault();
                var nomeArquivo = document.getElementById('nomeArquivo')
                    .value;
                var slug = document.getElementById('slugArquivo').value;
                console.log(slug);
                if (slug !== 'comece-do-zero') {
                    var zip = new JSZip();
                    zip.file(nomeArquivo + '.html', htmlFile);
                    zip.generateAsync({
                        type: 'blob',
                    }).then(
                        function (content) {
                            jQuery('body').toast({
                                position: 'top left _margin-top-3-100',
                                message: 'O download iniciará em breve.',
                                displayTime: 5000,
                                class: 'inverted green',
                                showProgress: 'bottom',
                            });
                            saveAs(content, nomeArquivo + '.zip');
                        },
                        function (e) {
                            jQuery('body').toast({
                                position: 'top left _margin-top-3-100',
                                message: e,
                                displayTime: 5000,
                                class: 'inverted red',
                                showProgress: 'bottom',
                            });
                        }
                    );
                } else {
                    jQuery('body').toast({
                        position: 'top left _margin-top-3-100',
                        message: 'Você não pode baixar um template vazio.',
                        displayTime: 5000,
                        class: 'inverted red',
                        showProgress: 'bottom',
                    });
                }
            });
            /*
				var contatos = document.getElementById('enderecoEnvio')
					.value;
				var assunto = document.getElementById('assuntoEnvio').value;
				//var html = JSON.stringify("{'num':" + htmlFile + "}");
	
				if (contatos == '' || assunto == '') {
					alert('Os campos precisam ser preenchidos.');
					return false;
				} else {
					var data = {
						action: 'emakbee_envia_teste',
						contatos: contatos,
						assunto: assunto,
						html: htmlFile,
						nonce: emakbee_infos.nonce_enviateste,
					};
					jQuery
						.post(ajaxURL, data, function (response) {
							//alert(response);
							var notificationArea = document.getElementById(
								'notificationArea'
							);
							notificationArea.innerHTML = response;
						})
						.done(jQuery('#enviaTeste').modal('hide'));
				}*/
        }),
        (bee_config.onError = function (errorMessage) {
            app.onError(errorMessage);
        });
    }

    function BeeEditor() {
        var that = this;
        this.plugin = undefined;

        this.start = function (token, bee_config) {
            this.container = document.getElementById(bee_config.container);
            this.container.style.position = 'absolute';
            this.container.style.width = '100%';
            this.container.style.height = '100%';
            offscreenElement(this.container);

            BeePlugin.create(token, bee_config, function (bee) {
                bee.start();
                that.plugin = bee;
            });
        };

        this.useTemplate = function (template) {
            this.template = template;
        };

        this.show = function () {
            if (!this.plugin) {
                setTimeout(function () {
                    that.show();
                }, 10);
                return;
            }

            this.plugin.start(this.template);

            onscreenElement(that.container);
            jQuery('.dimmer.carregando').hide();
        };

        this.hide = function () {
            offscreenElement(this.container);
        };
        return this;
    }

    function BeeSender() {
        this.thumbnail = undefined;
        this.start = function () {
            var iframe = document.createElement('iframe');
            iframe.style.position = 'absolute';
            iframe.style.top = '0';
            iframe.style.left = '0';
            iframe.style.width = '100%';
            iframe.style.height = '100%';
            document.body.appendChild(iframe);
            this.container = iframe;
            this.hide();
        };
        this.set_html = function (html) {
            this.html = html;
        };
        this.set_thumbnail = function (src) {
            this.thumbnail = src;
        };
        this.show = function () {
            this.container.contentWindow.document.body.innerHTML = this.html;
            onscreenElement(this.container);
        };
        this.hide = function () {
            offscreenElement(this.container);
        };
    }

    this.callbacks = new BeeCallbacks(this, bee_config);
    this.editor = new BeeEditor();
    this.sender = new BeeSender();

    this.start = function () {
        this.pluginContainer = document.getElementById(bee_config.container);
        this.editor.start(token, bee_config);
        this.editor.useTemplate(beetemplates);
        this.sender.start();
        this.editor.show(); // @god atenção aqui
    };

    this.onLoadEditor = function () {};

    this.onSend = function (html, thumbnail) {
        this.editor.hide();
        this.sender.set_html(html);
        this.sender.set_thumbnail(thumbnail);
        this.sender.show();
    };

    this.onError = function (errorMessage) {
        console.log(errorMessage);
        alert(errorMessage);
    };

    return this;
}

// Callback map.
//
// This creates a facility to create callbacks into the
// tutorial BEE application from the BEE configuration,
// which is defined and created before the BEE App is created.
BeeApp.callback = function (name) {
    return function () {
        BeeApp.singleton.callbacks[name].apply(null, arguments);
    };
};