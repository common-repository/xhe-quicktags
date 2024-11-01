<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Invalid request.' );
}

if ( ! class_exists( 'XHEQT_Admin' ) ) :
    class XHEQT_Admin
    {
        function __construct()
        {
            global $xhe_waqt_settings;
            add_filter( 'plugin_action_links', array( $xhe_waqt_settings, 'plugin_action_links' ), 10, 2 );

            add_action( 'admin_init', array( $xhe_waqt_settings, 'register_settings' ) );
            add_action( 'admin_init', array( $xhe_waqt_settings, 'redirect_exists_tabs') );
            add_action( 'admin_menu', array( $this, 'register_submenu' ) );

            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_script' ), 11 );
        }



        public function register_submenu() {
            global $xhe_waqt_settings;

            add_options_page(
                esc_html__('WP AddQuicktags', 'xhe-wpaqt'),
                esc_html__('WP AddQuicktags', 'xhe-wpaqt'),
                'manage_options',
                XHEQT::$pluginName . '-settings',
                array( $xhe_waqt_settings, 'submenu_page_callback' )
            );
        }

        public function admin_enqueue_script( $hook ) {
            if( empty($hook) || ! empty($hook) && $hook != sprintf('settings_page_%s-settings', XHEQT::$pluginName) ) {
               return;
            }

            wp_enqueue_style('select2', XHEQT_PLUGIN_URL . 'assets/third-party/css/select2.min.css', array(), '4.1.0', 'all' );
            wp_enqueue_script('select2', XHEQT_PLUGIN_URL . 'assets/third-party/js/select2.min.js', array('jquery'), '4.1.0' );
            wp_enqueue_style('xhe-quicktags', XHEQT_PLUGIN_URL . 'assets/css/admin.css', array(), XHEQT::$pluginVersion, 'all' );
            wp_enqueue_script( 'xhe-quicktags', XHEQT_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), XHEQT::$pluginVersion, true );
        }

    }

    new XHEQT_Admin();
endif;