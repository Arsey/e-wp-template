<?php
if (function_exists('add_theme_support')) {
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(150, 150); // default Post Thumbnail dimensions
}


if (function_exists('add_image_size')) {
   // add_image_size('portfolio', 220, 134);

}

if (function_exists('register_sidebars')) {
    //register_sidebars(4);
}

if (!function_exists('centita_posted_on')) :

    function centita_posted_on() {

        $cat_name = get_the_category();
        $comments_number = comments_number('No Comments', 'One Comment', '% Comments');

        printf(__('<span class="posted-detail">%1$s&nbsp&nbsp|&nbsp&nbsp' . $cat_name[0]->name . '&nbsp&nbsp|&nbsp&nbsp' . get_the_author() . '&nbsp&nbsp|&nbsp&nbsp' . $comments_number . '</span>', 'centita'), sprintf('%1$s', get_the_date())
        );
    }

endif;

function register_custom_menu() {
    register_nav_menu('custom_menu', __('Custom Menu'));

}

/*
 *
 *
        for layoyt custom menu use this:
        <?php wp_nav_menu(array('menu' => 'custom_menu'));?>
 *
 *
 */

/*-----------------------------*/
/*---------SEARCH FORM---------*/
/*-----------------------------*/

    function custom_search_form() {
    $form = '
        <div id="search-box">
            <form role="search" method="get" id="search" action="' . home_url('/') . '" >
                <p>
                    <input type="text" class="search-text" value="' . get_search_query() . '" name="s" id="s" onblur="if (this.value == \'\'){this.value = \'Search\'; }" onfocus="if (this.value == \'Search\') {this.value = \'\'; }"/>
                    <input type="image" class="go" src="' . get_bloginfo('template_directory') . '/img/search-icon.gif" />
                </p>
            </form>
       </div>';
    $style='
        <style>
            #search-box form *{outline:none;}
            #search-box form p{margin:0;padding:0;position:relative;display:inline-block;zoom:1;*display:inline;}
            #search-box form p input.search-text{padding: 2px 20px 3px 5px;border-radius: 3px;border: 1px solid grey;}
            #search-box form p input.go{position:absolute;top: 5px;right: 5px;}
        </style>';
    return $form.$style;
}

add_filter('get_search_form', 'custom_search_form');

//////////////////////////////////////////////
//how to use:   echo custom_search_form();
//////////////////////////////////////////////

/*-----------------------------*/
/*---------end of: SEARCH FORM---------*/
/*-----------------------------*/


?>
