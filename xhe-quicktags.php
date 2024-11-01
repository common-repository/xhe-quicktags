<?php
/*
Plugin Name: XHE QuickTags
Plugin URI: https://xhtmlexpert.com/plugins/wp-addquicktags
Description: Allows you to add buttons to the WordPress admin text editor.
Author: xHTMLExpert
Version: 1.0.0
Author URI: https://xhtmlexpert.com
*/

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Invalid request.' );
}

define( 'XHEQT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'XHEQT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! class_exists( 'XHEQT' ) ) :
    class XHEQT {
        static $plugin;
        static $pluginName = 'xheqt';
        static $pluginVersion = '1.0.0';

        static $post_types_js = array( 'comment', 'edit-comments', 'widgets' );

        private function __construct() {}

        static function init_actions() {
            self::$plugin = plugin_basename( __FILE__ );

            add_action( 'init', array( __CLASS__, 'admin_init' ) );
            add_action( 'plugins_loaded', array( __CLASS__, 'load_plugin_textdomain' ) );

            require_once XHEQT_PLUGIN_DIR . 'includes/helpers.php';
            require_once XHEQT_PLUGIN_DIR . 'includes/class-admin-settings.php';
            require_once XHEQT_PLUGIN_DIR . 'includes/class-admin.php';
            require_once XHEQT_PLUGIN_DIR . 'includes/class-admin-tinymce.php';
        }

        static function admin_init() {
            if ( ! is_admin() ) {
                return null;
            }

            foreach ( xhe_quicktag_admin_pages_js() as $page ) {
                add_action( 'admin_print_scripts-' . $page, array( __CLASS__, 'get_json' ) );
            }
        }

        static function get_json() {
            global $current_screen, $xhe_waqt_settings;

            $pattern = get_shortcode_regex();

            if ( null !== $current_screen->id && ! in_array($current_screen->id, xhe_quicktag_admin_post_types(), true) ) {
                return null;
            }

            $options = (array) get_option( $xhe_waqt_settings->getOptionName() );

            $options['button_quicktags'] = xhe_quicktag_format_repeater($options);

            foreach ($options['button_quicktags'] as $k => $button) {
                if (   preg_match( '/'. $pattern .'/s', $button['start_tag'], $matches ) ) {
                    $options['button_quicktags'][$k]['start_tag'] = do_shortcode($matches[0]);
                }
            }

            if ( empty( $options['button_quicktags'] ) ) {
                $options['button_quicktags'] = array();
            }?>
            <script type="text/javascript">
                var xhe_waqt_tags = <?php echo wp_json_encode( $options ); ?>,
                    xhe_waqt_post_type = <?php echo wp_json_encode( $current_screen->id ); ?>,
                    xhe_waqt_js = <?php echo wp_json_encode( xhe_quicktag_admin_post_types() ); ?>;
            </script>
            <?php
        }

        static function set_locale() {
            load_plugin_textdomain(
                'xhe_waqt',
                false,
                XHEQT_PLUGIN_DIR . '/languages/'
            );
        }
    }

    add_action( 'plugins_loaded', array( 'XHEQT', 'init_actions' ) );
endif;