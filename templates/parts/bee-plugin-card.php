<?php
$dimmer         = include plugin_dir_path(__FILE__) . 'bee-plugin-dimmer.php';

function hgodbee_plugin_card($taxs, $tags) {
	global $post;
	$last_id = get_post_meta(get_the_ID(), '_edit_last', true);
	if (empty($last_id)) {
		$autor = get_the_author_firstname();
	} else {
		$autor = get_the_modified_author();
	}
	?>
<div class="column templates template-<?php echo get_the_ID(); ?> ">
    <div class="ui teal fluid inverted card">
        <?php hgodbee_dimmer_carregando('card-dimmer'); ?>
        <div class="extra content">
        <?php $icon_size = '1.2em'; ?>
        <i class="right floated icon star" data-feather="star" width="<?php echo $icon_size; ?>"
                        height="<?php echo $icon_size; ?>"></i>
            <div class="header"><?php echo get_the_title(); ?></div>
        </div>

        <div class="content">
            <?php
if ('comece-do-zero' !== $post->post_name) {
    $icon_size = '0.9em';
		?>
            <div class="meta">
                <span class="_autor"><i data-feather="user" width="<?php echo $icon_size; ?>"
                        height="<?php echo $icon_size; ?>"></i>
                    <?php echo $autor; ?></span><br>
                <span class="_data"><i data-feather="calendar" width="<?php echo $icon_size; ?>"
                        height="<?php echo $icon_size; ?>"></i>
                    <?php echo get_the_modified_time('j/m/y', get_the_ID()); ?></span> <span class="_time"><i
                        data-feather="clock" width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>"></i>
                    <?php echo get_the_modified_time('h:i:s', get_the_ID()); ?></span>
            </div> <!-- .meta -->
            <?php
}
	?>

            <div class="ui bulleted inverted list">
                <?php
foreach ($taxs as $tax) {
		echo '<div class="item">' . $tax . '</div>';
	}
	?>
            </div><!-- .horizontal .list -->

            <div class="ui violet labels">
                <?php
foreach ($tags as $tag) {
		echo '<span class="ui inverted label _margin-top-3">' . $tag . '</span>';
	}
	?>
            </div><!-- .violet .labels -->


        </div>
        <div class="ui bottom attached buttons" style="margin-top:10px;">
            <?php
$edit_url = add_query_arg('action', 'edit', get_permalink());
	$view_url = add_query_arg('action', 'view', get_permalink());
	if ('comece-do-zero' === $post->post_name) {
		echo '<a href="' . $edit_url . '" class="ui teal button"><i class="icon" data-feather="edit-2" width="' . $icon_size. '" height="' . $icon_size . '"></i></a>';
	} else {
        $icon_size = '1.2em';
		?>
            <a href="<?php echo $edit_url; ?>" class="ui teal icon button"><i class="icon" data-feather="edit-2" width="<?php echo $icon_size; ?>"
                        height="<?php echo $icon_size; ?>"></i></a>
            <a href="<?php echo $view_url; ?>" class="ui blue icon button"><i class="icon" data-feather="eye" width="<?php echo $icon_size; ?>"
                        height="<?php echo $icon_size; ?>"></i></a>
            <a href="#" class="ui yellow icon button"><i class="icon" data-feather="archive" width="<?php echo $icon_size; ?>"
                        height="<?php echo $icon_size; ?>"></i></a>
            <a href="#" id="<?php echo get_the_ID(); ?>" class="ui red icon button delete"><i data-feather="trash-2" width="<?php echo $icon_size; ?>"
                        height="<?php echo $icon_size; ?>"></i></a>
            <?php }
	?>

        </div>
    </div>
</div>
<?
}