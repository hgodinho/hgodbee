<?php

function hgodbee_plugin_card($terms) {
    global $post;
?>
<div class="column">
    <div class="ui teal fluid raised card">
        <div class="content">
            <i class="right floated star icon"></i>
            <div class="header"><?php echo get_the_title();?></div>
            <div class="meta" style="display:inherit!important">
                <div class="ui green grey labels">
                <?php
                    foreach( $terms as $term ){
                        echo '<span class="ui label _margin-top-3">' . $term . '</span>';
                    }
                ?>
                </div>
            </div> 
        </div>
        <div class="ui bottom attached buttons" style="margin-top:10px;">
        <?php
            $edit_url = add_query_arg( 'action', 'edit', get_permalink() );
            if ( 'comece-do-zero' !== $post->post_name ) { 
                $view_url = add_query_arg( 'action', 'view', get_permalink() );
                echo '<a href="' . $view_url . '" class="ui blue button">Visualizar</a>';
            }
            ?>
            <a href="<?php echo $edit_url; ?>" class="ui teal button">Editar</a>
            <a href="<?php the_permalink();?>" class="ui red icon button"><span
                    class="dashicons dashicons-trash"></span></a>
        </div>
    </div>
</div>
<?
}