<?php
/**
 * Template part - Inicializa o BeePlugin
 */
 function hgodbee_plugin_starter($token, $uid, $template) {
     ?>
        <script id="beepluginstarter" type="text/javascript">
            jQuery(document).ready(function () {
                var template = <?php echo $template; ?>;
                //var template = <?php //echo json_encode($template, JSON_HEX_QUOT | JSON_HEX_TAG ); ?>;
                var token = <?php echo $token; ?>;
                var pluginUID = '<?php echo $uid; ?>';
                var app = new window.BeeApp(
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