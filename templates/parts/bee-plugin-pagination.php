<?php
/**
 * @param WP_Query|null $wp_query
 * @param bool $echo
 *
 * @return string
 * Accepts a WP_Query instance to build pagination (for custom wp_query()),
 * or nothing to use the current global $wp_query (eg: taxonomy term page)
 * - Tested on WP 4.9.5
 * - Tested with Bootstrap 4.1
 * - Tested on Sage 9
 *
 * USAGE:
 *     <?php echo bootstrap_pagination(); ?> //uses global $wp_query
 * or with custom WP_Query():
 *     <?php
 *      $query = new \WP_Query($args);
 *       ... while(have_posts()), $query->posts stuff ...
 *       echo bootstrap_pagination($query);
 *     ?>
 */
function hgodbee_pagination(WP_Query $wp_query = null, $paged = 'paged', $echo = true)
{

    if (null === $wp_query) {
        global $wp_query;
    }

    if ($paged != 'paged') {
        
        $pages = paginate_links([
            'base' => get_permalink() . '%_%',
            'format' => '/page/%#%',
            'current' => max(1, get_query_var($paged)),
            'total' => $wp_query->max_num_pages,
            'type' => 'array',
            'show_all' => false,
            'end_size' => 1,
            'mid_size' => 1,
            'prev_next' => true,
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'add_args' => false,
            'add_fragment' => '',
        ]
        );
    } else {
        $pages = paginate_links([
            'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
            'format' => '/page/%#%',
            'current' => max(1, get_query_var($paged)),
            'total' => $wp_query->max_num_pages,
            'type' => 'array',
            'show_all' => false,
            'end_size' => 2,
            'mid_size' => 2,
            'prev_next' => true,
            'prev_text' => __('<i class="icon" data-feather="arrow-left" width="16px" height="16px"></i>'),
            'next_text' => __('<i class="icon" data-feather="arrow-right" width="16px" height="16px"></i>'),
            'add_args' => false,
            'add_fragment' => '',
        ]
        );
    }

    if (is_array($pages)) {
        $pagination = '<div class="ui inverted pagination menu no-width">';

        foreach ($pages as $page) {
            $semantic_page = substr_replace($page, 'yellow item ', strpos($page, 'page-numbers'), 0);
            $semantic_page = substr_replace($semantic_page, strpos($page, 'current') !== false ? 'active ' : '', strpos($page, 'page-numbers'), 0);
            $pagination .= $semantic_page;
        }

        $pagination .= '</div>';

        if ($echo) {
            echo $pagination;
        } else {
            return $pagination;
        }
    }

    return null;
}
