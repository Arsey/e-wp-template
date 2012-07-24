<?php
/* For adding custop settings for theme we need to use next file */
/* ------------------------------------------------------------- */
#include('theme-options.php');



/* WORK WITH I-M-A-G-E-S */
/* For add theme support thumbnails */
/* ------------------------------------------------------------- */
if (function_exists('add_theme_support')) {
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(150, 150); // default Post Thumbnail dimensions
}


/* If we need some custom cropped images after downloading to server we need to use */
/* ------------------------------------------------------------- */
#if (function_exists('add_image_size')) {
#   add_image_size('some-size', $width, $height);
#}

/* this function display image of any post if it has thumbnail or attachments */
/* ------------------------------------------------------------- */

function get_image_any_case($id, $size) {
    if (function_exists("has_post_thumbnail") && has_post_thumbnail()) {
        echo get_the_post_thumbnail($id, $size);
    } else {
        $args = array(
            'post_type' => 'attachment',
            'numberposts' => 1,
            'post_mime_type' => 'image',
            'post_parent' => $id,
            'sort_order' => 'menu_order'
        );
        $attachments = get_posts($args);
        if ($attachments) {
            $title = htmlspecialchars($attachments[0]->post_title);
            echo '<img src="', wp_get_attachment_thumb_url($attachments[0]->ID), '" class=""  alt="', $title, '" title="', $title, '" />';
        }
    }
}

/* WORK WITH M-E-N-U-S */
/* For using dynamic menus */
/* ------------------------------------------------------------- */
#add_action('init', 'register_custom_menus');
#function register_custom_menus() {
#    register_nav_menu('main_menu', __('Main Menu'));
#    register_nav_menu('footer_menu', __('Footer Menu'));
#}



/* WORK WITH S-I-D-E-B-A-R-S */
#if (function_exists('register_sidebar')) {
#    register_sidebar(array(
#        'name' => 'One',
#        'id' => 'one-sidebar',
#    ));
#    register_sidebar(array(
#        'name' => 'One another sidebar',
#        'id' => 'one-another-sidebar',
#    ));
#}



/* WORK WITH C-O-M-M-E-N-T-S */
if (!function_exists('twentyten_comment')) :
    function twentyten_comment($comment, $args, $depth) {
        $GLOBALS['comment'] = $comment;
        switch ($comment->comment_type) :
            case '' :
                ?>
                <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
                    <div id="comment-<?php comment_ID(); ?>">
                        <div class="comment-author vcard">
                            <?php echo get_avatar($comment, 40); ?>
                            <?php printf(__('%s <span class="says">says:</span>', 'twentyten'), sprintf('<cite class="fn">%s</cite>', get_comment_author_link())); ?>
                        </div><!-- .comment-author .vcard -->
                        <?php if ($comment->comment_approved == '0') : ?>
                            <em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.', 'twentyten'); ?></em>
                            <br />
                        <?php endif; ?>

                        <div class="comment-meta commentmetadata"><a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
                                <?php
                                /* translators: 1: date, 2: time */
                                printf(__('%1$s at %2$s', 'twentyten'), get_comment_date(), get_comment_time());
                                ?></a><?php edit_comment_link(__('(Edit)', 'twentyten'), ' ');
                                ?>
                        </div><!-- .comment-meta .commentmetadata -->

                        <div class="comment-body"><?php comment_text(); ?></div>

                        <div class="reply">
                            <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                        </div><!-- .reply -->
                    </div><!-- #comment-##  -->

                    <?php
                    break;
                case 'pingback' :
                case 'trackback' :
                    ?>
                <li class="post pingback">
                    <p><?php _e('Pingback:', 'twentyten'); ?> <?php comment_author_link(); ?><?php edit_comment_link(__('(Edit)', 'twentyten'), ' '); ?></p>
                    <?php
                    break;
            endswitch;
        }
    endif;


    /* O-T-H-E-R      F-U-N-C-T-I-O-N-S */

    /* this function cut any text to max length */

    function cut_text($string, $max_length) {
        if (strlen($string) > $max_length) {
            $string = mb_substr($string, 0, $max_length);
            $pos = strrpos($string, " ");
            if ($pos === false) {
                return mb_substr($string, 0, $max_length) . "...";
            }
            return mb_substr($string, 0, $pos) . "...";
        } else {
            return $string;
        }
    }

    /* function for pagination links on posts in loop */

    function pagination($title = 'Page:&nbsp;&nbsp;', $mux_num_pages, $type = 'plain') {
        if ($mux_num_pages > 1) {
            echo '<div class="pagination">' . $title;

            global $wp_query;
            $big = 999999999; // need an unlikely integer
            $pagination = paginate_links(array(
                'base' => str_replace($big, '%#%', get_pagenum_link($big)),
                'format' => '?paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total' => $mux_num_pages,
                'prev_next' => false,
                'type' => $type,
                    ));

            if ($type == 'plain') {
                echo $pagination;
            } else if ($type = 'array') {
                foreach ($pagination as $link) {
                    echo preg_replace('~<a~', "$0 rel=nofollow ", $link);
                }
            }
            ?>
        </div>
        <?php
    }
}

/* this function return count of posts in category */

function wt_get_category_count($input = '') {
    global $wpdb;
    if ($input == '') {
        $category = get_the_category();
        return $category[0]->category_count;
    } elseif (is_numeric($input)) {
        $SQL = "SELECT $wpdb->term_taxonomy.count FROM $wpdb->terms, $wpdb->term_taxonomy WHERE $wpdb->terms.term_id=$wpdb->term_taxonomy.term_id AND $wpdb->term_taxonomy.term_id=$input";
        return $wpdb->get_var($SQL);
    } else {
        $SQL = "SELECT $wpdb->term_taxonomy.count FROM $wpdb->terms, $wpdb->term_taxonomy WHERE $wpdb->terms.term_id=$wpdb->term_taxonomy.term_id AND $wpdb->terms.slug='$input'";
        return $wpdb->get_var($SQL);
    }
}

/* this function returning a slag of any posts category */

function get_cat_slug($cat_id) {
    $cat_id = (int) $cat_id;
    $category = &get_category($cat_id);
    return $category->slug;
}

/*
 * This function return links with pagination of all posts on site
 * it's return an array. [2] - just a paginated links, [0] - links on posts an paginated links
 */

function arsey_sitemap() {
    $returning = array();
    $current_cat_id = $_REQUEST['cid'];
    if ((!is_numeric($current_cat_id) && isset($_REQUEST['cid']))) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . get_bloginfo('url') . '/sitemap');
    } else if (!is_array(term_exists(get_cat_slug($current_cat_id), 'category')) && isset($_REQUEST['cid'])) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . get_bloginfo('url') . '/sitemap');
    }
    if (isset($_REQUEST['offset']) && $_REQUEST['offset'] != 0) {
        $q_offset = '&offset=' . $_REQUEST['offset'];
    }
    $category_ids = get_all_category_ids();
    $i = 0;
    $glc = 0;
    $max_posts = 500;
    $b = 0;
    $links = '';
    $home = get_settings('home');
    foreach ($category_ids as $cat_id) {
        if ($cat_id != 1) {
            $i++;
            $glc++;
            $subpages = 1;
            if (!$current_cat_id && ($i == 1)) {
                $current_cat_id = $cat_id;
            }

            $count = wt_get_category_count($cat_id);
            if ($count != 0) {
                if ($count > $max_posts) {
                    $subs = ($count - $count % $max_posts) / $max_posts;
                    if ($subs > 1) {
                        $subpages = $subs;
                        if (($count - $count % $max_posts) - $max_posts > 0) {
                            $subpages++;
                        }
                    }
                }

                $offset = '';
                $class = '';
                for ($j = 1; $j <= $subpages; $j++) {
                    if ($subpages > 1 && $j != 1) {
                        $glc++;
                        $offset = '&offset=' . ($max_posts * $j - $max_posts);
                    }
                    if ($current_cat_id == $cat_id) {
                        $class = 'class="active"';
                    }
                    $href = $home . '/sitemap/?cid=' . $cat_id . $offset;
                    $links.='<a ' . $class . ' href="' . $href . '" title="' . get_cat_name($cat_id) . '">' . $glc . '</a><span> | </span>';
                }
                $subpages = 1;
            }
        }
    }

    $cat_name = get_cat_name($current_cat_id);
    $returning[1] = $custom_title = get_the_title() . ' - Раздел ' . $cat_name;
    ?>
    <style>
        .arsey-sitemap{margin-left: 10px;}
        .arsey-sitemap .sitemap-links a.active{text-decoration: none ;color: grey;font-size: 18px;margin: 0 3px 0px 3px;border-bottom: 0;}
        .arsey-sitemap .sitemap-links a{color:#000 !important}
        .arsey-sitemap .sitemap-links a:hover{color:#E95D0F !important;}
        .arsey-sitemap h3{margin:20px 0;font-size:16px;}
        .arsey-sitemap .sitemap-links{margin: 10px 0 10px -5px;background: #FBDECF;padding: 4px 9px 6px 5px;font-size: 13px;display: inline-block;border-radius: 4px;border: 1px solid #DFDFDF;box-shadow: 2px 2px 6px #E95D0F;}
        .arsey-sitemap  .sitemap-list{margin-bottom:20px;}
    </style>
    <?php
    $links = '<div class="sitemap-links">' . $links . '</div>';
    $sitemap_title = '<h3>Раздел - ' . $cat_name . ':</h3>';
    $sitemap_list = '';

    $sitemap_query = new WP_Query('showposts=' . $max_posts . '&category__in=' . $current_cat_id . $q_offset);

    while ($sitemap_query->have_posts()) {
        $sitemap_query->the_post();
        $b++;
        $sitemap_list.='<li><a href="' . get_permalink() . '" rel="bookmark" title="Permanent Link to ' . get_the_title() . '">' . get_the_title() . '</a></li>';
    }
    wp_reset_query();
    $sitemap_list = '<ul class="sitemap-list">' . $sitemap_list . '</ul>';

    $returning[0] = '<div class="arsey-sitemap">' . $links . $sitemap_title . $sitemap_list . $links . '</div>';
    $returning[2] = $links;
    return $returning;
}

