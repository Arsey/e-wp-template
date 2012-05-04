<?php
while (have_posts()) {

    the_post();
    ?><div class="textshadow"><h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1></div>
    
        <?php the_content('more'); ?>
    
    <?php
    }
    ?>

    <div class="pagination"><!-- page pagination -->                                       
    <?php
    if ($wp_query->max_num_pages > 1)
        echo 'Page:&nbsp;&nbsp;';
    ?>
        <?php
        global $wp_query;
        $big = 999999999; // need an unlikely integer
        echo paginate_links(array(
            'base' => str_replace($big, '%#%', get_pagenum_link($big)),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $wp_query->max_num_pages,
            'prev_next' => false
        ));
        ?>
</div>

