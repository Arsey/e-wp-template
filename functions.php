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


?>
