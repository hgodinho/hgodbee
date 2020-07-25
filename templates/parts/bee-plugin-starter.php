<?php
/**
 * Template part - Inicializa o BeePlugin
 */

 function hgodbee_plugin_starter($token, $uid, $template) {
     ?>

        <script id="beepluginstarter" type="text/javascript">
            jQuery(document).ready(function () {
                //var autosavejson = '<?php //echo $autosavejson; ?>';

                var template = <?php echo $template; ?> ;
                console.log(template);
                var token = <?php echo $token; ?> ;
                console.log(token);
                var pluginUID = '<?php echo $uid; ?>';
                console.log(pluginUID);
                /*
                if (autosavejson != 0) {
                    templates.unshift({
                        name: 'Auto salvo',
                        json: autosavejson,
                        excerpt: 'Salvo automaticamente a cada 30 segundos. <p class="mt-3"><small>*deixa de existir ao salvar. última edição por: <?php echo $user->user_login; ?></small></p>',
                    })
                } */

                var app = new BeeApp(
                    token,
                    getBeeConfig(
                        pluginUID, // user_id
                        'bee-plugin-container' // container_id
                    ),
                    template
                );
                app.start();
            });
        </script>

     <?php
 }