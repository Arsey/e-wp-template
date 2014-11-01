<?php

function p($var, $print = true)
{
    if ($print === true) {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    } elseif ($print === false) {
        ob_start();
        echo '<pre>';
        print_r($var);
        echo '</pre>';
        $out = ob_get_contents();
        ob_clean();
        return $out;
    }
}

/**
 * Registering custom post types
 */
add_action('init', 'create_post_type');
function create_post_type()
{
    /**
     * Slides
     */
    register_post_type(
        'theme_slides',
        array(
            'labels' => array(
                'name' => __('Slides'),
                'singular_name' => __('Slide')
            ),
            'public' => true,
            'has_archive' => true,
            'exclude_from_search' => true,
            'supports' => array(
                'thumbnail',
                'title'
            )
        )
    );
}

/**
 * For adding the custom settings for theme we need to use next file
 */
include('functions/theme-options.php');
include('functions/metabox.php');

/**
 * Make the theme supports thumbnails
 */
if (function_exists('add_theme_support')) {
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(150, 150); // default Post Thumbnail dimensions
}

/**
 * Custom image sizes
 */
#if (function_exists('add_image_size')) {
#   add_image_size('some-size', $width, $height);
#}

/**
 * This function displays an image of the post if it has the thumbnail or the attachments
 * @param $id
 * @param $size
 */
function get_image_any_case($id, $size)
{
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

/**
 * Allow SVG through WordPress Media Uploader
 * @param $mimes
 * @return mixed
 */
function cc_mime_types($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}

add_filter('upload_mimes', 'cc_mime_types');

/**
 * Dynamic menus
 */
add_action('init', 'register_custom_menus');
function register_custom_menus()
{
    register_nav_menu('main_menu', __('Main Menu'));
    register_nav_menu('services-menu', __('Services Menu'));
}


function build_tree(array &$elements, $parentId = 0)
{
    $branch = array();
    foreach ($elements as $element) {
        if ($element->menu_item_parent == $parentId) {
            $children = build_tree($elements, $element->ID);
            if ($children) {
                $element->children = $children;
            }
            $branch[$element->ID] = $element;
            unset($elements[$element->ID]);
        }
    }
    return $branch;
}

/**
 * Dynamic sidebars
 */
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

/**
 * This function cuts any text to max length
 * @param $string
 * @param $max_length
 * @return string
 */
function cut_text($string, $max_length)
{
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

/**
 * Pagination function
 * @param $title
 * @param $mux_num_pages
 * @param string $type
 */
function pagination($title = 'Pages: ', $mux_num_pages = 3, $type = 'plain')
{
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

/**
 * Returns the number of category posts
 * @param string $input
 * @return mixed
 */
function wt_get_category_count($input = '')
{
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

/**
 * Returns category slug by category id
 * @param $cat_id
 * @return mixed
 */
function get_cat_slug($cat_id)
{
    $cat_id = (int)$cat_id;
    $category = & get_category($cat_id);
    return $category->slug;
}

/**
 * Shortcut function for bloginfo('template_directory')
 * @param bool $print_out
 * @return string|void
 */
function td($print_out = true)
{
    if ($print_out)
        bloginfo('template_directory');
    else
        return get_bloginfo('template_directory');
}

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
