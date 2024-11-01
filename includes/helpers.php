<?php
function xhe_quicktag_key_first($array) {
    reset($array);
    return key($array);
}

function xhe_quicktag_fields($field_name, $field, $valueField, $table = true) {
    $placeholderField = isset($field['placeholder']) ? $field['placeholder'] : '';

    $field['value'] = isset($field['value']) ? $field['value'] : '';
    $valueField = empty($valueField) ? $field['value'] : $valueField;

    if( $table ) {
        printf(
            '<tr valign="top"><th scope="row" class="titledesc"><label for="%s">%s</label></th><td class="forminp forminp-text">',
            esc_attr($field_name),
            esc_attr($field['label'])
        );
    }else {
        if( ! isset($field['hide_label']) ) {
            printf(
                '<label for="%s">%s</label>',
                esc_attr($field_name),
                esc_attr($field['label'])
            );
        }
    }

    $fieldName = isset($field['name']) ? esc_attr($field['name']) : esc_attr($field_name);
    $suffix = isset($field['id_suffix']) ? '_' . $field['id_suffix'] : '';

    switch ($field['type']) {
        case 'text':
            printf(
                '<input name="%1$s" id="%2$s" type="text" class="xhe-control" value="%3$s" placeholder="%4$s">',
                esc_attr($fieldName),
                esc_attr($field_name . $suffix),
                esc_attr($valueField),
                esc_attr($placeholderField)
            );
            if( isset($field['field_desc']) ) {
                echo '<p style="font-size: 12px; font-style: italic; color: #1f1f1f;">'. esc_attr($field['field_desc']) .'</p>';
            }

            break;
        case 'checkbox':
            printf(
                '<input name="%1$s" id="%2$s" type="checkbox" class="xhe-control xhe-checkbox-field" value="%3$s" placeholder="%4$s"%5$s>',
                esc_attr($fieldName),
                esc_attr($field_name . $suffix),
                empty($field['cbvalue']) ? 1 : esc_attr($field['cbvalue']),
                esc_attr($placeholderField),
                ($field['cbvalue'] == $valueField) ? ' checked' : ''
            );

            if( isset($field['field_desc']) ) {
                echo '<p style="font-size: 12px; font-style: italic; color: #1f1f1f;">'. esc_attr($field['field_desc']) .'</p>';
            }

            break;
        case 'textarea':
            $row = isset($field['rows']) ? absint($field['rows']) : 3;

            printf(
                '<textarea name="%1$s" id="%2$s" class="xhe-control" placeholder="%4$s" rows="%5$d">%3$s</textarea>',
                esc_attr($fieldName),
                esc_attr($field_name . $suffix),
                isset($field['html']) ? esc_html($valueField) : esc_attr($valueField),
                esc_attr($placeholderField),
                absint($row)
            );
            if( isset($field['field_desc']) ) {
                echo '<p style="font-size: 12px; font-style: italic; color: #1f1f1f;">'. esc_attr($field['field_desc']) .'</p>';
            }

            break;
        case 'tags':
            $valueField = empty($valueField) ? [] : json_decode(base64_decode($valueField));

            printf(
                '<select name="%1$s[]" id="%2$s" class="xhe-control xhe-tags" placeholder="%3$s" multiple="multiple">',
                esc_attr($fieldName),
                esc_attr($field_name . $suffix),
                esc_attr($placeholderField)
            );

            if( isset($field['options']) ) {
                printf(
                    '<option value="">%s</option>',
                    esc_attr($placeholderField)
                );

                foreach ( $field['options'] as $option_name => $option_value ) {
                    $selected = '';
                    if( ! empty($valueField) && in_array($option_name, $valueField) ) {
                        $selected = ' selected';
                    }

                    echo $selected;

                    printf(
                        '<option value="%s"%s>%s</option>',
                        $option_name,
                        $selected,
                        $option_value
                    );
                }
            }


            print '</select>';
            break;
        case 'repeater':
            $fields = $field['fields'];
            if( ! empty($valueField) && is_array($valueField) ) {
                $firstKey = xhe_quicktag_key_first($valueField);
            }

            include XHEQT_PLUGIN_DIR . 'templates/html-admin-repeater-field.php';
            break;
    }

    if( $table ) {
        print('</td></tr>');
    }
}

function xhe_quicktag_admin_pages_js() {
    return array(
        'post.php',
        'post-new.php',
        'comment.php',
        'edit-comments.php',
        'widgets.php',
    );
}

function xhe_quicktag_admin_post_types() {
    $post_types = get_post_types(array('show_ui' => true));
    $post_types = array_values($post_types);
    return array_merge($post_types, XHEQT::$post_types_js);
}

function xhe_quicktag_format_repeater($options) {
    $new_options = array();

    if( ! empty($options) ) {
        foreach ( $options['button_quicktags']['button_label'] as $option_key => $option_value ) {
            $new_options[$option_key] = array(
                'button_label' => esc_attr($option_value),
                'start_tag' => $options['button_quicktags']['start_tag'][$option_key],
                'end_tag' => $options['button_quicktags']['end_tag'][$option_key],
                'visual' => absint($options['button_quicktags']['visual'][$option_key])
            );

            $post_types = json_decode(base64_decode($options['button_quicktags']['post_type'][$option_key]));
            if( ! empty($post_types) ) {
                $new_post_types = array();
                foreach ($post_types as $post_type) {
                    $new_post_types[$post_type] = 1;
                }

                $new_options[$option_key] = $new_options[$option_key] + $new_post_types;
            }
        }

        $new_options = array_values($new_options);
    }

    return $new_options;
}