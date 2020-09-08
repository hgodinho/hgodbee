/**
 * Sidebar Color Palette
 * 
 * @author  hgodinho
 * @version 1.0
 */

(function ($) {
    $(document).ready(function () {

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
         * 
         * {AJAX}
         */
        $('.save-colors').unbind('click').click(function () {
            event.preventDefault();

            $('.wide.sidebar').dimmer({
                displayLoader: true,
                loaderVariation: 'slow orange medium elastic',
                loaderText: 'Salvando, aguarde...'
            }).dimmer('show');
            
            var data = {
                action: 'hgodbee_save_colors',
                palette: colorPalette,
                nonce: hgodbee_palette.nonce_save_colors,
            };

            var settings = {
                url: hgodbee_palette.ajax_url,
                data: data,
                success: function (response) {
                    $('.wide.sidebar').dimmer('hide');
                    if (typeof response !== 'undefined' && response != '') {
                        response = JSON.parse(response);
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
                    }
                },
                global: false,
            };
            $.post(settings);
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
            htmlSwatchColor('000000', name);
            htmlSwatchColor('999999', name);
            htmlSwatchColor('FFFFFF', name);

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
                        addSwatchColor(color.toHex(), name);
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
                    addSwatchColor(color, name);
                });
            }

            /**
             * Adiciona swatch de cor na paleta
             */
            function addSwatchColor(hexColor, name) {
                if (hexColor != null) {
                    htmlSwatchColor(hexColor, name);

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
            function htmlSwatchColor(hexColor, name) {
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