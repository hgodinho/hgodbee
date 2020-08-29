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

        let colorPalette = [];

        /**
         * Popup com as opções das cores
         */
        $('.wide.sidebar').on('click', '.column.color', function (event) {
            event.preventDefault();
            $(this)
                .popup({
                    position: 'top left',
                    forcePosition: true,
                    hoverable: true,
                    preserve: true,
                })
                .popup('show');
        });

        $('#categoriasTemplate').dropdown({
            allowAditions: true,
            cleareable: true,
            action: 'activate',
        });

        /**
         * Usa ResizeSensor para mudar a classe do botão salvar paleta para ele ficar
         * fixed ou sticky de acordo com a altura do accordion-wraper
         */
        var accordion = $('.accordion-wraper');
        new ResizeSensor(accordion, function () {
            if (accordion.height() < 315) {
                $('div.save-colors').addClass('_fixed-save-colors');
            } else {
                $('div.save-colors').removeClass('_fixed-save-colors');
            }
        });

        /**
         * função de copiar para a área de transferência no clique do
         * botão presente na popup de opções das cores.
         */
        $(document).on('click', '.copy-color', function (event) {
            event.preventDefault();
            var color = $(this).data('color');
            const el = document.createElement('input');
            el.value = color;
            el.setAttribute('readonly', '');
            el.style.position = 'absolute';
            el.style.left = '-999px';
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            $(this)
                .popup({
                    html: '<strong>Copiado!</strong> <span style="color:' +
                        color +
                        ';">' +
                        color +
                        '</span>',
                    on: 'click',
                    closable: true,
                })
                .popup('show');
            document.body.removeChild(el);
        });

        /**
         * função de deletar a cor da paleta.
         */
        $(document).on('click', '.delete-color', function (event) {
            event.preventDefault();
            var gridClass = $(this).data('class');
            var color = $(this).data('color');
            var grid = $('.grid.' + gridClass);
            var palette = grid.find('#' + color);
            palette.hide('slow', function () {
                $(this).remove();
            });

            colorPalette.filter(function (v) {
                if (v[0].key === gridClass) {
                    v[0].colors.filter(function (c, i) {
                        if (c === '#' + color) {
                            v[0].colors.splice(i, 1);
                        }
                    });
                }
            });
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
         * Dropdown de pesquisa de categorias na paleta de cores
         */
        $('select.forcolors').dropdown({
            allowAditions: true,
            cleareable: true,
            onAdd: function (addedValue, addedText) {
                var colors = $('option.' + addedValue).data('colors');
                addAccordionTemplate(addedText, addedValue, colors);
            },
            onRemove: function (removedValue, removedText) {
                removeAccordionTemplate(removedText, removedValue);
            },
        });

        /**
         * Loop através do data-terms do html que contém os termos presentes no post
         */
        var terms = $('.accordion-wraper').data('terms');
        $.each(terms, function (i) {
            var colors = $('option.' + terms[i].slug).data('colors');
            addAccordionTemplate(terms[i].name, terms[i].slug, colors);
        });

        /**
         * Abre sidebar paleta de cores
         */
        $('.forcolors').addClass('hide');
        $('.cores').unbind('click').click(function () {
            $('.ui.sidebar')
                .sidebar('setting', {
                    transition: 'overlay',
                    onVisible: function () {
                        $('.forcolors').removeClass('hide');
                    },
                    onHidden: function () {
                        $('.forcolors').addClass('hide');
                    },
                }).sidebar('toggle');
        });


        /**
         * Salva Cores.
         */
        $('.save-colors').unbind('click').click(function () {
            event.preventDefault();
            console.log(colorPalette);
            var data = {
                action: 'hgodbee_save_colors',
                palette: colorPalette,
                nonce: hgodbee_object.nonce_save_colors,
            };
            console.log(data);
            $.post(ajaxURL, data, function (response) {
                response = JSON.parse(response);
                console.log(response);
                if (1 == response.success) {
                    $('body').toast({
                        position: 'top left _margin-top-3-100',
                        message: '<strong>Paleta salva</strong>',
                        displayTime: 5000,
                        class: 'inverted green',
                        showProgress: 'bottom',
                    });
                }
                if (0 == response.success) {
                    $('body').toast({
                        position: 'top left _margin-top-3-100',
                        message: '<strong>Algo de errado não está certo.</strong>',
                        displayTime: 5000,
                        class: 'inverted red',
                        showProgress: 'bottom',
                    });
                }
            }).done(function () {
                $('.wide.sidevar').hide();
            });
        });

        /**
         * Função do botão delete no arquivo de templates
         */
        $('.template-delete').unbind('click').click(function () {
            event.preventDefault();
            var id = $(this).attr('id');
            var page = $(this).attr('data-page');
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
                    nonce: hgodbee_object.nonce_delete,
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
                            window.location.replace(hgodbee_object.archive);
                        }
                    },
                    global: false,
                };

                $.post(settings);
            });
        });

        /**
         * Salva como template
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
                nonce: hgodbee_object.nonce_save_as_template,
            };

            $.post(ajaxURL, data, function (response) {
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

        /**
         * Gera o accordion com spectrum na sidebar.
         */
        function addAccordionTemplate(title, name, colors = []) {
            $('.accordion-wraper').append(
                '<button class="accordion ' +
                name +
                '">' +
                title +
                '</button>\
                <div class="panel">\
                    <div class="ui six column left aligned padded grid ' +
                name +
                '"></div>\
                <input type="text" id="' +
                name +
                '" class="paleta" /></div>\
                '
            );

            /**
             * Animação no click do accordion
             */
            var accordion = $('button.' + name);
            accordion[0].addEventListener('click', function () {
                event.preventDefault();
                $(this).toggleClass('active');
                var panel = $(this).next();
                if (panel.css('maxHeight') !== '0px') {
                    panel.css('maxHeight', '0px');
                } else {
                    panel.css('maxHeight', '500px');
                }
            });

            /**
             * Adiciona Cores default: preto, branco e cinza, MAS NAO SALVA!
             */
            html_swatch_color('000000', name);
            html_swatch_color('999999', name);
            html_swatch_color('FFFFFF', name);

            /**
             * Configurações do Spectrum
             */
            $('#' + name).spectrum({
                preferredFormat: 'rgb',
                showInitial: true,
                showInput: true,
                showPalette: true,
                palette: [
                    ['white', '#999', 'black'],
                    ['#009045', '#4caf50', '#fb9b14'],
                ],
                showSelectionPalette: true,
                maxSelectionSize: 3,
                chooseText: 'selecionar',
                cancelText: 'cancelar',
                change: function (color) {
                    if (typeof color !== 'undefined') {
                        add_swatch_color(color.toHex(), name);
                    }
                },
            });
            var size = '1.7em';
            $('.sp-dd').html(
                feather.icons['plus-square'].toSvg({
                    width: size,
                    height: size,
                    'stroke-width': 1,
                })
            );

            /**
             * Adiciona as swatch colors passadas a paleta.
             */
            if (typeof colors[0] !== 'undefined' && colors[0].length > 0) {
                $.each(colors[0], function (i) {
                    var color = colors[0][i].replace('#', '');
                    add_swatch_color(color, name);
                });
            }

            /**
             * Adiciona swatch de cor na paleta
             */
            function add_swatch_color(hexColor, name) {
                if (hexColor != null) {
                    html_swatch_color(hexColor, name);

                    colorPalette.filter(function (v) {
                        if (v[0].key === name) {
                            v[0].colors.push('#' + hexColor);
                        }
                    });
                }
            }

            /**
             * Insere o html que define a swatch color
             */
            function html_swatch_color(hexColor, name) {
                $('.grid.' + name).append(
                    '<div id=\'' +
                    hexColor +
                    '\' class="column color" style="background-color:#' +
                    hexColor +
                    '" data-html="<div class=\'content\'>\
                                        <div class=\'ui segment\' style=\'background-color:#' +
                    hexColor +
                    '\'><p class=\'color-string\'>#' +
                    hexColor +
                    '</p>\
                                        </div>\
                                        <div class=\'ui bottom attached buttons\' tabindex=\'0\'>\
                                            <div class=\'ui green button copy-color\' data-color=\'#' +
                    hexColor +
                    '\'>Copiar</div>\
                                            <div class=\'ui red button delete-color\' data-class=\'' +
                    name +
                    '\' data-color=\'' +
                    hexColor +
                    '\'>Deletar</div>\
                                        </div>">' +
                    feather.icons.edit.toSvg({
                        width: '1.2em',
                        height: '1.2em',
                        class: 'edit-color-icon',
                    }) +
                    '</span>\
                        </div>'
                );
            }

            /**
             * Verifica se existem cores na swatch
             */
            var swatches = [];
            if (typeof colors[0] !== 'undefined' && colors[0].length > 0) {
                swatches = colors[0];
            }

            /**
             * Define a Paleta
             */
            let palette = [{
                key: name,
                title: title,
                colors: swatches,
            }, ];

            /**
             * adiciona paleta.
             */
            colorPalette.push(palette);
        }

        /**
         * Remove o accordion com spectrum da sidebar.
         */
        function removeAccordionTemplate(value, slug) {
            var accordion = $('button.accordion.' + slug);
            var panel = accordion.next();
            panel.hide(500, function () {
                $(this).remove();
            });
            accordion.hide(500, function () {
                $(this).remove();
            });

            colorPalette.map(function (e) {
                if (slug == e[0].key) {
                    colorPalette.filter(function (v, i) {
                        if (v[0].key === slug) {
                            colorPalette.splice(i, 1);
                        }
                    });
                }
            });
        }
    });
})(jQuery);

/**
 * Função para retornar o valor do parâmetro de url solicitado.
 */
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

/**
 * função para retornar as configurações do BeePlugin, usada únicamente no single
 */
function getBeeConfig(user_or_account_id, container_id) {
    return {
        uid: '__' + user_or_account_id,
        container: container_id,
        preventClose: true,
        autosave: 30,
        language: 'pt-BR',
    };
}

/**
 * BeeApp
 * @param {array} token
 * @param {array} bee_config
 * @param {array} beetemplates
 */
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
                action: 'hgodbee_autosave',
                json: jsonFile,
                nonce: hgodbee_object.nonce_autosave,
            };
            var settings = {
                url: ajaxURL,
                data: data,
                success: function (response) {
                    if (typeof response !== 'undefined' && response != '') {
                        response = JSON.parse(response);
                        if (response.success == 0) {
                            jQuery('body').toast({
                                // erro
                                position: 'top left _margin-top-3-100',
                                message: response.msg,
                                displayTime: 5000,
                                class: 'inverted red',
                                showProgress: 'bottom',
                            });
                        }
                        if (response.success == 1) {
                            jQuery('body').toast({
                                // sucesso
                                position: 'top left _margin-top-3-100',
                                message: response.msg,
                                displayTime: 5000,
                                class: 'inverted green',
                                showProgress: 'bottom',
                            });
                        }
                    }
                },
                global: false,
            };
            jQuery.post(settings);
        }),
        (bee_config.onSave = function (jsonFile, htmlFile) {
            jQuery('#templateSave')
                .modal({
                    inverted: false,
                })
                .modal('show');

            jQuery('#salvarTemplateBTN').unbind('click').click(function () {
                event.preventDefault();
                console.log('#salvarTemplateBTN');
                var templateID = jQuery('input[name=templateID').val();
                var templateName = document.getElementById('nomeTemplate')
                    .value;
                var categories = [];
                jQuery('#categoriasTemplate :selected').each(function() {
                    categories.push(jQuery(this).text());
                });
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

            jQuery('#salvarNovoBTN').unbind('click').click(function () {
                event.preventDefault();
                console.log('#salvarNovoBTN');
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
            jQuery('#salvarTemplateBTNnull').unbind('click').click(function () {
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
            jQuery('#downloadBTN').unbind('click').click(function () {
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
            //jQuery('.dimmer.carregando').hide();
            jQuery('.field.colors').toggleClass('hide');
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

    this.onLoadEditor = function () {
        jQuery('.dimmer.carregando').hide();
    };

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