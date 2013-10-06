<?php
$themename = "Theme name";
$shortname = "shortthemename";
$options = array();

add_action('init', 'theme_options');

function theme_options() {
    global $themename, $shortname, $options;
    $options = include('theme-custom-options.php');
}

add_action('admin_menu', 'theme_add_admin');

function theme_add_admin() {
    global $themename, $shortname, $options;

    if ($_GET['page'] == basename(__FILE__)) {
        if ('save' == $_REQUEST['action']) {
            check_admin_referer('theme-save');
            foreach ($options as $value) {
                if (isset($_REQUEST[$value['id']])) {
                    update_option($value['id'], $_REQUEST[$value['id']]);
                } else {
                    delete_option($value['id']);
                }
            }

            header("Location: themes.php?page=theme-options.php&saved=true");
            die;
        } else if ('reset' == $_REQUEST['action']) {
            check_admin_referer('theme-reset');
            foreach ($options as $value) {
                delete_option($value['id']);
            }
            header("Location: themes.php?page=theme-options.php&reset=true");
            die;
        }
    }

    add_theme_page($themename . " Options", "$themename Options", "edit_themes", basename(__FILE__), 'theme_admin');
}

function theme_admin() {
    global $themename, $shortname, $options;
    theme_options_css_js();
    if ($_REQUEST['saved']) {
        ?>
        <div id="message" class="updated fade">
            <p><strong><?php echo $themename; ?> options were saved</strong></p>
        </div>
        <?php
    }

    if ($_REQUEST['reset']) {
        ?>
        <div id="message" class="updated fade">
            <p><strong><?php echo $themename; ?> options were reset</strong></p>
        </div>
        <?php
    }
    ?>
    <div class="wrap">
        <h2><?php echo $themename; ?> Options</h2>
        <form method="post">
            <?php wp_nonce_field('theme-save'); ?>

            <div class="metabox-holder">
                <?php
                foreach ($options as $value) {

                    switch ($value['type']) {
                        case 'box_begin':
                            echo '<div class="postbox"><h3>' . $value['name'] . '</h3><div class="inside">';
                            break;
                        case 'box_end':
                            echo '</div></div>';
                            break;
                        case 'text':
                            ?>
                            <label>
                                <span><?php echo $value['name']; ?>:</span>
                                <input size="<?php echo $value['size']; ?>" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="text" value="<?php echo stripslashes(get_option($value['id'], $value['std'])); ?>"/>
                                <span class="description"><?php echo $value['desc']; ?></span>
                            </label>
                            <?php
                            break;
                        case 'select':
                            ?>
                            <tr valign="top">
                                <th scope="row"><?php echo $value['name']; ?></th>
                                <td>
                                    <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
                                        <option value="">--</option>
                                        <?php
                                        foreach ($value['options'] as $key => $option) {
                                            if ($key == get_option($value['id'], $value['std'])) {
                                                $selected = "selected=\"selected\"";
                                            } else {
                                                $selected = "";
                                            }
                                            ?>
                                            <option value="<?php echo $key ?>" <?php echo $selected ?>>
                                                <?php echo $option; ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php echo $value['desc']; ?>
                                </td>
                            </tr>
                            <?php
                            break;


                        case 'textarea':
                            $ta_options = $value['options'];
                            ?>

                            <label>
                                <span><?php echo $value['name']; ?>:</span>
                                <textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" cols="<?php echo $ta_options['cols']; ?>" rows="<?php echo $ta_options['rows']; ?>"> <?php echo stripslashes(get_option($value['id'], $value['std'])); ?></textarea>
                                <span class="description"><?php echo $value['desc']; ?></span>
                            </label>
                            <?php
                            break;


                        case "radio":
                            ?>
                            <tr valign="top">
                                <th scope="row"><?php echo $value['name']; ?>:</th>
                                <td>
                                    <?php
                                    foreach ($value['options'] as $key => $option) {
                                        if ($key == get_option($value['id'], $value['std'])) {
                                            $checked = "checked=\"checked\"";
                                        } else {
                                            $checked = "";
                                        }
                                        ?>
                                        <input type="radio"
                                               name="<?php echo $value['id']; ?>"
                                               value="<?php echo $key; ?>"
                                               <?php echo $checked; ?>
                                               /><?php echo $option; ?>
                                        <br />
                                    <?php } ?>
                                    <?php echo $value['desc']; ?>
                                </td>
                            </tr>
                            <?php
                            break;
                        case "checkbox":
                            ?>
                            <tr valign="top">
                                <th scope="row"><?php echo $value['name']; ?></th>
                                <td>
                                    <?php
                                    if (get_option($value['id'])) {
                                        $checked = "checked=\"checked\"";
                                    } else {
                                        $checked = "";
                                    }
                                    ?>
                                    <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?>                                           />
                                    <?php echo $value['desc']; ?>
                                </td>
                            </tr>
                            <?php
                            break;
                        default:
                            break;
                    }
                }
                ?>
            </div>
            <div class="clear"></div>
            <p class="submit">
                <input name="save" type="submit" value="Сохранить изменения" class="button-primary" />
                <input type="hidden" name="action" value="save" />
            </p>
        </form>

        <form method="post">
            <?php wp_nonce_field('theme-reset'); ?>
            <p class="submit">
                <input name="reset" type="submit" value="Сбросить настройки" />
                <input type="hidden" name="action" value="reset" />
            </p>
        </form>
    </div>
    <?php
}

function theme_options_css_js() {
    echo <<<CSS
    <style type="text/css">
	.metabox-holder { width: 700px; float: left;margin: 0; padding: 0 10px 0 0;line-height:26px;}
        .metabox-holder label{margin-bottom: 5px;display: inline-block;border: 1px dashed #E5E5E5;padding: 5px;border-radius: 5px;background: #F2F2F2;}
	.metabox-holder .postbox .inside {padding: 0 10px;}
        input, textarea, select {margin: 5px 0 5px 0;padding: 1px;}
	.small {font-family:Calibri, Arial;font-size:9px;}
        input[type="text"] {width: 250px;height: 32px;}
        input[type="text"], select, textarea {padding: 8px;line-height: 22px;}
        label, label span {display: block;}
        </style>
CSS;
    echo <<<JS
<script type="text/javascript">jQuery(document).ready(function($) {
    $(".fade").fadeIn(1000).fadeTo(1000, 1).fadeOut(1000);
});
</script>
JS;
}
?>