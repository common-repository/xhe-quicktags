<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Invalid request.' );
}

if ( ! class_exists( 'XHEQT_TinyMCE' ) ) :
    class XHEQT_TinyMCE
    {
        function __construct() {
            add_filter( "mce_external_plugins", array( $this, 'add_externel_buttons' ) );
            add_filter( "mce_buttons", array( $this, 'add_editor_buttons' ) );
        }

        public function add_externel_buttons( $plugins ) {
            if ( ! is_array( $plugins ) ) {
                $plugins = array();
            }

            $plugins['wp_addquicktags'] = XHEQT_PLUGIN_URL . 'assets/js/wysiwyg-editor.js';
            return $plugins;
        }

        public function add_editor_buttons( $buttons, $editor_id = FALSE ) {
            array_push($buttons, 'wp_addquicktags' );
            return $buttons;
        }

    }

    new XHEQT_TinyMCE();
endif;