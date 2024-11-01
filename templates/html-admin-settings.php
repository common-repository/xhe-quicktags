<div class="wrap">
    <h1><?php echo get_admin_page_title();?></h1>

    <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
        <?php
        foreach( $this->tabs as $tab_id => $tab_label ) {
            $pageUrl = ( $tab_id == xhe_quicktag_key_first($this->tabs) ) ? esc_url($pageUrl) : add_query_arg('tab', esc_attr($tab_id), esc_url($pageUrl));
            printf(
                '<a href="%s" class="nav-tab%s">%s</a>',
                esc_url($pageUrl),
                ($tab_id == $this->tabActive) ? ' nav-tab-active' : '',
                esc_attr($tab_label)
            );
        }?>
    </nav>

    <?php
        if( isset($_SESSION['error']) ) {
            printf('<div id="message" class="error inline"><p>%s</p></div>', esc_attr($_SESSION['error']));
        }

        if( isset($_SESSION['success']) ) {
            printf('<div id="message" class="updated inline"><p>%s</p></div>', esc_attr($_SESSION['success']));
        }

        if( ! empty($fields) ) { ?>
        <form method="post" action="options.php">
            <table class="form-table">
                <?php foreach ($fields as $field_name => $field) {
                    $defaultValue = isset($field['value']) ? esc_attr($field['value']) : '';
                    $valueField = isset($fieldValue[$field_name]) ? wp_unslash($fieldValue[$field_name]) : esc_attr($defaultValue);

                    $field['name'] = sprintf('%s[%s]', esc_attr($this->optionName), esc_attr($field_name));

                    xhe_quicktag_fields($field_name, $field, $valueField);
                }?>
            </table>

            <p class="submit">
                <?php submit_button( 'Save Settings' );?>
                <?php wp_nonce_field( XHEQT::$pluginName . 'setting_admin' );?>
                <?php settings_fields( $this->optionName . '_group' );?>
            </p>
        </form>
    <?php }?>
</div>

<style>
    #message {
        margin-top: 10px;
    }
</style>