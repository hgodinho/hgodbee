<?php
wp_head();
if( is_user_logged_in() ){

    if (wp_script_is('hgodbee_react') /*&& wp_script_is('emakbee_app')*/) {
    ?>
    <!--
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
   -->
<div id="notificationArea">
</div>

<style type="text/css">
    #bee-plugin-container {
        position: absolute;
        width: 100%;
        height: 100%;
    }

    a.continue {
        display: none;
    }
</style>


<div id="react-app"></div>

<?php

        $token = get_option('BeePlugin_token');
        $clientUID = get_option('emakbee_settings');
        $clientUID = $clientUID['emakbee_client_uid'];
        
        $args = array(
            'post_type' => 'template_bee',
            'order' => 'ASC',
            'orderby' => 'date',
            'posts_per_page' => -1,
        );
       // $template_query = new WP_Query($args);
        $templates = array();
        
        /*
        while ( $template_query->have_posts()) : $template_query->the_post();
            $update = get_the_modified_time('j/n/y à\s g:i:s' , get_the_ID() );
            $author = get_the_author_meta('user_login');
            //var_dump($update);
            $template = array(
                'name' => get_the_title(),
                'json' => get_the_content(),
                'excerpt' => get_the_excerpt(),
                'update' => $update,
                'author' => $author,
            );
            array_unshift($templates, $template);
        endwhile;
        wp_reset_query();
        */
        
        
        $autosavejson = get_transient( 'emakbee_autosave' );
        
        if ( false === ( $autosavejson = get_transient( 'emakbee_autosave' ))){
         //run if no valid transient   
            $autosavejson = 0;
        }

        $templatesJSON = json_encode($templates);
        //var_dump($autosavejson);
        if ( false === ( $user_id = get_transient('emakbee_user'))){
            $user_id = 0;
        }
        $user = get_user_by( 'ID', $user_id );
        ?>

<script id="beepluginstarter" type="text/javascript">
    jQuery(document).ready(function () {
        var autosavejson = '<?php echo $autosavejson; ?>';

        var templates = <?php echo $templatesJSON; ?> ;
        var token = <?php echo json_encode($token); ?> ;
        var pluginUID = '<?php echo $clientUID; ?>';

        if (autosavejson != 0) {
            templates.unshift({
                name: 'Auto salvo',
                json: autosavejson,
                excerpt: 'Salvo automaticamente a cada 30 segundos. <p class="mt-3"><small>*deixa de existir ao salvar. última edição por: <?php echo $user->user_login; ?></small></p>',
            })
        }

        var app = new BeeApp(
            token,
            getBeeConfig(
                pluginUID, // user_id
                'bee-plugin-container' // container_id
            ),
            templates
        );
        app.start();
    });
</script>

<?php
    }  
    else{
        echo '<h2>Scripts não carregados.</h2>';
    }
} else{
    echo '<h2>usuário não logado.</h2>';
}
wp_footer();