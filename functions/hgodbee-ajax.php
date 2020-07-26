<?php
$config = include dirname(plugin_dir_path(__FILE__)) . '/config/config.php';

    /**
     * AJAX FUNTIONS!
     */
    /**
     * Retrieves the Bee Token
     *
     * You must have a developer account at https://developers.beefree.io/
     *
     * @return void
     */
    function hgodbee_generate_bee_token(){
        function hgodbee_token() {
            HGodBee::hb_log($_POST, 'title', __CLASS__, __METHOD__, __LINE__);
            $nonce = check_ajax_referer($config['prefix'] . 'admin', 'nonce');
            //$nonce = wp_verify_nonce( $_POST['nonce'], $config['prefix'] . 'admin' );
            HGodBee::hb_log($nonce, 'title', __CLASS__, __METHOD__, __LINE__);
            
            global $wpdb;
            $options       = get_option($config['prefix'] . 'settings_name');
            $client_id     = $config['prefix'] . 'id';
            $client_secret = $options[$config['prefix'] . 'secret'];
            $beeplugin     = new BeeFree($client_id, $client_secret);
            $token         = $beeplugin->getCredentials();

            if (isset($token->access_token)) {
                $token_saved = update_option($config['prefix'] . 'token', $token);

                if (true === $token_saved) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>';
                    echo 'Token gerado e salvo com sucesso.';
                    echo '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>';
                    echo 'Token NÃO salvo.';
                    echo '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            }
            wp_die();
        }
        add_action('wp_ajax_hgodbee_token', 'hgodbee_token');
    }


    /**
     * Save Template
     *
     * @return void
     */
    function hgodbee_save_template() {
        HGodBee::hb_log('imgere', 'title', __CLASS__, __METHOD__, __LINE__);  

      $nonce = check_ajax_referer($config['prefix'] . 'save_as_template', 'nonce');
        global $wpdb;
        $json_template = $_POST['json'];
        $template_name = $_POST['name'];
        $template_description = $_POST['dsc'];
        $cpt = $this->cpt['name'];
        if( post_exists($template_name) ){
            $page_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '".$template_name."' AND post_type = '".$cpt."'");
            $template_update = array(
                'ID' => $page_id,
                'post_content' => $json_template,
                'post_excerpt' => $template_description,
            );
            wp_update_post($template_update);
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>';
            echo 'Template atualizado com sucesso: </strong>';
            echo $template_name . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            if ( current_user_can('edit_posts') ){
                $saved = wp_insert_post(array(
                    'post_content' => $json_template,
                    'post_title' => $template_name,
                    'post_excerpt' =>  $template_description,
                    'post_status' => 'publish',
                    'post_type' => $cpt,
                    )
                );
                HGodBee::hb_log($saved, $cpt, __CLASS__, __METHOD__, __LINE__);
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>';
                echo 'Template criado com sucesso: </strong>';
                echo $template_name . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                echo 'sem permissão';
            }
        }
        delete_transient('emakbee_autosave');
        delete_transient('emakbee_user');
        wp_die();
    }
    add_action('wp_ajax_hgodbee_save_template', 'hgodbee_save_template');
