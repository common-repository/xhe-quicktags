<div id="repeater-<?php echo esc_attr($field_name);?>-wrapper" class="xhe-repeater">
    <table class="xhe-table">
        <thead>
            <tr>
                <th class="xhe-row-handle"></th>
                <?php foreach ($fields as $field_id => $_field) {
                    printf(
                        '<th class="xhe-th" id="%s-th"%s>%s</th>',
                        esc_attr($field_id),
                        empty( $_field['style'] ) ? '' : ' style="' . esc_attr($_field['style']) . '"',
                        esc_attr($_field['label'])
                    );
                }?>
                <th class="xhe-row-handle"></th>
            </tr>
        </thead>

        <tbody class="ui-sortable">
            <?php
            if( isset($firstKey) && isset($valueField[$firstKey]) ) {
                $i = 1;
                foreach ($valueField[$firstKey] as $row_id => $row) { ?>
                <tr id="<?php echo esc_attr($field_name);?>-<?php echo esc_attr($row_id);?>-row" class="xhe-row" data-id="<?php echo esc_attr($row_id);?>">
                    <td class="xhe-row-handle order ui-sortable-handle" title="Drag to reorder">
                        <span><?php echo esc_attr($i);?></span>
                    </td>

                    <?php foreach ($fields as $field_id => $data_field) {
                        if( isset($field['hide_label']) ) {
                            $data_field['hide_label'] = esc_attr($field['hide_label']);
                        }

                        $_fieldName = isset($field['name']) ? esc_attr($field['name']) : esc_attr($field_name);

                        $data_field['name'] = sprintf(
                            '%s[%s][%s]',
                            esc_attr($_fieldName),
                            esc_attr($field_id),
                            esc_attr($row_id)
                        );

                        if( isset($data_field['html']) ) {
                            $data_value_field = isset($valueField[$field_id][$row_id]) ? esc_html($valueField[$field_id][$row_id]) : '';
                        }else {
                            $data_value_field = isset($valueField[$field_id][$row_id]) ? esc_attr($valueField[$field_id][$row_id]) : '';
                        }

                        $data_field['id_suffix'] = esc_attr($row_id);
                        ?>
                    <td class="acf-field" data-name="<?php echo esc_attr($field_id);?>" data-type="<?php echo esc_attr($data_field['type']);?>"<?php echo empty( $data_field['style'] ) ? '' : ' style="' . esc_attr($data_field['style']) . '"';?>>
                        <?php xhe_quicktag_fields($field_name . '-' . $field_id, $data_field, $data_value_field, false);?>
                    </td>
                    <?php }?>

                    <td class="xhe-row-handle">
                        <a class="xhe-icon -plus" href="#" data-event="add-row" data-repeater_id="<?php echo esc_attr($field_name);?>" title="<?php esc_html_e('Add Row', 'xhe_waqt');?>"></a>
                        <a class="xhe-icon -minus" href="#" data-event="remove-row" data-repeater_id="<?php echo esc_attr($field_name);?>" title="<?php esc_html_e('Remove Row', 'xhe_waqt');?>"></a>
                    </td>
                </tr>
                <?php $i++;
                }
            }?>
        </tbody>
    </table>

    <div class="xhe-actions">
        <a class="xhe-button button button-primary" href="#" data-event="add-row" data-repeater_id="<?php echo esc_attr($field_name);?>"><?php esc_html_e('Add Row', 'xhe_waqt');?></a>
    </div>
</div>

<script id="tpl-repeater-<?php echo esc_attr($field_name);?>" type="text/template">
    <tr id="[:repeater_name]-[:id]-row" class="xhe-row" data-id="[:id]">
        <td class="xhe-row-handle order ui-sortable-handle" title="Drag to reorder">
            <span>[:order]</span>
        </td>
        <?php foreach ($fields as $field_id => $_field) {
            if( isset($field['hide_label']) ) {
                $_field['hide_label'] = esc_attr($field['hide_label']);
            }

            $_fieldName = isset($field['name']) ? esc_attr($field['name']) : esc_attr($field_name);
            $_field_id = sprintf('%s[%s][[:id]]', esc_attr($_fieldName), esc_attr($field_id));
            ?>
            <td class="acf-field acf-field-text" data-name="<?php echo esc_attr($field_id);?>" data-type="<?php echo esc_attr($_field['type']);?>"<?php echo empty( $_field['style'] ) ? '' : ' style="' . esc_attr($_field['style']) . '"';?>>
                <?php xhe_quicktag_fields($_field_id, $_field, '', false);?>
            </td>
        <?php }?>

        <td class="xhe-row-handle">
            <a class="xhe-icon -plus" href="#" data-event="add-row" data-repeater_id="[:repeater_name]" title="<?php esc_html_e('Add Row', 'xhe_waqt');?>"></a>
            <a class="xhe-icon -minus" href="#" data-event="remove-row" data-repeater_id="[:repeater_name]" title="<?php esc_html_e('Remove Row', 'xhe_waqt');?>"></a>
        </td>
    </tr>
</script>