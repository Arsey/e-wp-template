<?php

$meta_box = require_once('metabox_fields.php');

add_action('admin_menu', 'e_wp_theme_add_box');

// Add meta box
function e_wp_theme_add_box()
{
    global $meta_box;
    foreach ($meta_box as $mb) {
        add_meta_box($mb['id'], $mb['title'], 'kiwi_theme_show_box', $mb['page'], $mb['context'], $mb['priority']);
    }
}

// Callback function to show fields in meta box
function kiwi_theme_show_box()
{
    global $meta_box, $post;
    foreach ($meta_box as $mb) {
        if (in_array($post->post_type, explode(',', $mb['page']))) {
            echo meta_fields_html($mb['fields'], $post);
        }
    }
}

function meta_fields_html($fields, $post)
{
    // Use nonce for verification
    $str = '<input type="hidden" name="kiwi_theme_meta_box_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';
    $str .= '<table class="form-table">';

    foreach ($fields as $field) {
        // get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);

        $str .= '<tr>';
        if ($field['name']) {
            $str .= '<th style="width:20%"><label for="' . $field['id'] . '">' . $field['name'] . '</label></th>';
            $str .= '<td>';
        } else {
            $str .= '<td colspan="2">';
        }

        switch ($field['type']) {
            case 'text':
                $str .= '<input type="text" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . ($meta ? $meta : $field['std']) . '" size="30" style="width:97%" /><br />' . $field['desc'];
                break;
            case 'textarea':
                $str .= '<textarea name="' . $field['id'] . '" id="' . $field['id'] . '" cols="60" rows="4" style="width:97%">' . ($meta ? $meta : $field['std']) . '</textarea><br />' . $field['desc'];
                break;
            case 'select':
                $str .= '<select name="' . $field['id'] . '" id="' . $field['id'] . '">';
                foreach ($field['options'] as $option) {
                    $str .= '<option ' . ($meta == $option ? ' selected="selected"' : '') . '>' . $option . '</option>';
                }
                $str .= '</select>';
                break;
            case 'radio':
                foreach ($field['options'] as $option) {
                    $str .= '<input type="radio" name="' . $field['id'] . '" value="' . $option['value'] . '"' . ($meta == $option['value'] ? ' checked="checked"' : '') . ' />' . $option['name'];
                }
                break;
            case 'checkbox':
                $str .= '<input type="checkbox" name="' . $field['id'] . '" id="' . $field['id'] . '"' . ($meta ? ' checked="checked"' : '') . ' />';
                break;
        }
        $str .= '</td><td></td></tr>';
    }

    $str .= '</table>';

    return $str;
}

add_action('save_post', 'kiwi_theme_save_data');

// Save data from meta box
function kiwi_theme_save_data($post_id)
{
    global $meta_box, $post;
    // verify nonce

    if (!wp_verify_nonce($_POST['kiwi_theme_meta_box_nonce'], basename(__FILE__)))
        return $post_id;

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;


    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    foreach ($meta_box as $mb) {
        if (in_array($post->post_type, explode(',', $mb['page']))) {
            foreach ($mb['fields'] as $field) {
                $old = get_post_meta($post_id, $field['id'], true);
                $new = $_POST[$field['id']];
                if ($new && $new != $old) {
                    update_post_meta($post_id, $field['id'], $new);
                } elseif ('' == $new && $old) {
                    delete_post_meta($post_id, $field['id'], $old);
                }
            }
        }
    }
}
