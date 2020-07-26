<?php

function hgodbee_plugin_card() {
?>
<div class="column">
    <div class="ui teal fluid raised card">
        <div class="content">
            <i class="right floated star icon"></i>
            <div class="header"><?php echo get_the_title();?></div>
            <div class="meta" style="display:inherit!important">2 days ago</div>
        </div>
        <div class="content">
            <div class="ui segment">
                <p></p>
                <p></p>
                <p></p>
            </div>
        </div>
        <div class="ui bottom attached buttons" style="margin-top:10px;">
            <?php
                $edit_url = add_query_arg( 'action', 'edit', get_permalink() );
                $view_url = add_query_arg( 'action', 'view', get_permalink() );
                ?>
            <a href="<?php echo $edit_url; ?>" class="ui teal button">Editar</a>
            <a href="<?php echo $view_url; ?>" class="ui blue button">Visualizar</a>
            <a href="<?php the_permalink();?>" class="ui red icon button"><span
                    class="dashicons dashicons-trash"></span></a>
        </div>
    </div>
</div>
<?
}