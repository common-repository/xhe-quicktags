<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Invalid request.' );
}

if ( ! class_exists( 'XHEQT_Admin_Settings' ) ) :
    class XHEQT_Admin_Settings
    {
        public $tabs = array();
        public $fields = array();

        private $currentTab;
        private $tabActive;
        private $optionName = '';


        function __construct() {
            $this->setTabs();
            $this->setFields();

            $this->optionName = XHEQT::$pluginName . '_settings';
        }

        private function setTabs() {
            $this->tabs = apply_filters( 'xhe_wp_quicktags_tabs', array(
                'general' => 'General'
            ) );

            $this->currentTab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : '';

            $this->tabActive = empty($_GET['tab']) ? xhe_quicktag_key_first($this->tabs) : $this->currentTab;
        }

        private function setFields() {
            $this->fields = [
                'general' => [
                    'button_quicktags' => [
                        'label' => esc_html__('Quicktags Button', 'xhe_waqt'),
                        'type' => 'repeater',
                        'fields' => [
                            'button_label' => [
                                'label' => esc_html__('Button Label', 'xhe_waqt'),
                                'type' => 'text',
                                'style' => 'padding-left: 15px'
                            ],
                            'start_tag' => [
                                'label' => esc_html__('Start Tag', 'xhe_waqt'),
                                'type' => 'textarea',
                                'rows' => 4,
                                'html' => true,
                                'style' => 'padding-left: 15px'
                            ],
                            'end_tag' => [
                                'label' => esc_html__('End Tag', 'xhe_waqt'),
                                'type' => 'textarea',
                                'rows' => 4,
                                'html' => true,
                                'style' => 'padding-left: 15px'
                            ],
                            'post_type' => [
                                'label' => esc_html__('Post Type', 'xhe_waqt'),
                                'type' => 'tags',
                                'style' => 'padding-left: 15px'
                            ],
                            'visual' => [
                                'label' => esc_html__('Visual', 'xhe_waqt'),
                                'type' => 'checkbox',
                                'cbvalue' => 1,
                                'style' => 'width: 42px; text-align: center;'
                            ],
                        ],
                        'hide_label' => true
                    ],
                ]
            ];
        }



        /**
         * Register settings for options
         *
         * @uses    register_setting
         * @access  public
         * @since   2.0.0
         * @return  void
         */
        public function register_settings() {
            register_setting( $this->optionName . '_group', $this->optionName, array( $this, 'validate_settings' ) );
        }

        public function validate_settings( $values ) {
            $buttons   = array();
            $fields = isset($this->fields[$this->tabActive]) ? $this->fields[$this->tabActive] : array();

            if( ! empty($fields) ) {
                foreach ($fields as $field_name => $field) {
                    if( isset($field['type']) && $field['type'] == 'repeater' ) {
                        foreach ( $field['fields'] as $repeaterFieldName => $repeaterField ) {
                            if( isset($values[$field_name][$repeaterFieldName]) && is_array($values[$field_name][$repeaterFieldName]) ) {
                                foreach ( $values[$field_name][$repeaterFieldName] as $repeater_id => $repeater_value ) {
                                    if( isset($repeaterField['html']) ) {
                                        $value = wp_kses_stripslashes($repeater_value);
                                    }else {
                                        $value = sanitize_text_field($repeater_value);
                                    }

                                    if( ! empty($repeater_value) && is_array($repeater_value) ) {
                                        $value = base64_encode(json_encode($repeater_value));
                                    }


                                    $values[$field_name][$repeaterFieldName][$repeater_id] = $value;
                                }
                            }
                        }
                    }else {
                        if( $field['type'] == 'checkbox' ) {
                            $values[$field_name]  = isset($values[$field_name]) ? $values[$field_name] : '';
                        }else {
                            $values[$field_name]  = sanitize_text_field($values[$field_name]);
                        }


                    }
                }
            }

            return $values;
        }


        public function submenu_page_callback() {
            $optionName = sprintf(XHEQT::$pluginName . '_%s_settings', $this->tabActive );
            $pageUrl = menu_page_url(XHEQT::$pluginName . '-settings', false);

            $fields = isset($this->fields[$this->tabActive]) ? $this->fields[$this->tabActive] : array();
            $fieldValue = wp_parse_args( get_option($this->optionName), $this->getDefaultField($fields) );

            $fields['button_quicktags']['fields']['post_type']['options'] = $this->getPostTypes();

            include_once XHEQT_PLUGIN_DIR . 'templates/html-admin-settings.php';
        }

        public function redirect_exists_tabs() {
            if( ! isset($this->tabs[$this->tabActive]) ) {
                $pageUrl = menu_page_url(XHEQT::$pluginName . '-settings', false);

                wp_redirect($pageUrl);
                exit();
            }
        }

        public function plugin_action_links( $links, $file ) {

		if ( XHEQT::$plugin === $file ) {
			$links[] = '<a href="options-general.php?page=' . esc_attr(XHEQT::$pluginName) . '-settings">' . esc_html__(
					'Settings'
				) . '</a>';
		}

    return $links;
}

        protected function getDefaultField($fields) {
            $data = [];
            foreach ($fields as $_field_name => $_field) {
                $data[$_field_name] = isset($_field['value']) ? esc_attr($_field['value']) : '';
            }

            return $data;
        }

        protected function getPostTypes() {

            $dropdowns = array();
            $post_types = get_post_types( array(
                'public'   => true
            ), 'objects' );

            unset($post_types['attachment']);

            foreach ($post_types as $post_type => $labels) {
                $dropdowns[$post_type] = sprintf('%s — %s', esc_attr($labels->label), esc_attr($post_type));
            }

            return $dropdowns;
        }

        public function getOptionName() {
            return esc_attr($this->optionName);
        }
    }

    $GLOBALS['xhe_waqt_settings'] = new XHEQT_Admin_Settings();
endif;