<?php

function tdir($echo=0) {
    if ($echo == 0) {
        return $temp_dir = get_bloginfo('template_directory') . '/';
    } else if ($echo == 1) {
        echo $temp_dir = get_bloginfo('template_directory') . '/';
    }
}
?>
