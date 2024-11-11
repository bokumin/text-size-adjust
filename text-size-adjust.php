<?php
/**
 * Plugin Name: Text Size Adjust
 * Plugin URI: https://github.com/bokumin/text-size-adjust
 * Description: WordPress plugin to globally configure text sizes
 * Version: 1.0.0
 * Author: bokumin
 * Author URI: https://bokumin45.server-on.net
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: text-size-adjust
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GTS_VERSION', '1.0.0');
define('GTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GTS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load text domain for translations
add_action('init', function() {
    load_plugin_textdomain('global-text-size', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// Include required files
require_once GTS_PLUGIN_DIR . 'includes/class-admin-settings.php';
require_once GTS_PLUGIN_DIR . 'includes/class-frontend-styles.php';

class Text_Size_Adjust {
    private static $instance = null;
    private $admin_settings;
    private $frontend_styles;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', array($this, 'init'), 0);
    }

    public function init() {
        if (is_admin()) {
            $this->admin_settings = new GTS_Admin_Settings();
        }
        $this->frontend_styles = new GTS_Frontend_Styles();
        $this->register_assets();
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_assets'));
    }

    public function register_assets() {
        wp_register_style(
            'global-text-size-admin',
            GTS_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            GTS_VERSION
        );

        wp_register_script(
            'global-text-size-editor',
            GTS_PLUGIN_URL . 'assets/js/text-size-support.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
            GTS_VERSION,
            true
        );

        wp_set_script_translations('global-text-size-editor', 'global-text-size', GTS_PLUGIN_DIR . 'languages');
    }

    public function enqueue_editor_assets() {
        wp_enqueue_script('global-text-size-editor');
    }
}

function text_size_adjust_init() {
    return Text_Size_Adjust::get_instance();
}

add_action('plugins_loaded', 'text_size_adjust_init');

register_uninstall_hook(__FILE__, 'text_size_adjust_uninstall');

function text_size_adjust_uninstall() {
    delete_option('global_text_size_settings');
}
