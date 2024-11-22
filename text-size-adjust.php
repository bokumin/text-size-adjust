<?php
/**
 * Plugin Name: Text Size Adjust
 * Plugin URI: https://github.com/bokumin/text-size-adjust
 * Description: WordPress plugin to globally configure text sizes
 * Version: 1.1.2
 * Author: bokumin
 * Author URI: https://bokumin45.server-on.net
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: text-size-adjust
 */

if (!defined('ABSPATH')) {
    exit;
}

define('TEXT_SIZE_ADJUST_VERSION', '1.0.0');
define('TEXT_SIZE_ADJUST_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TEXT_SIZE_ADJUST_PLUGIN_URL', plugin_dir_url(__FILE__));

add_action('init', function() {
    load_plugin_textdomain('text-size-adjust', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

require_once TEXT_SIZE_ADJUST_PLUGIN_DIR . 'includes/class-admin-settings.php';
require_once TEXT_SIZE_ADJUST_PLUGIN_DIR . 'includes/class-frontend-styles.php';

class Text_Size_Adjust_Plugin {
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
            $this->admin_settings = new Text_Size_Adjust_Admin_Settings();
        }
        $this->frontend_styles = new Text_Size_Adjust_Frontend_Styles();
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_assets'));
    }

    public function register_assets() {
        wp_register_style(
            'text-size-adjust-admin',
            TEXT_SIZE_ADJUST_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            TEXT_SIZE_ADJUST_VERSION
        );

        wp_register_script(
            'text-size-adjust-editor',
            TEXT_SIZE_ADJUST_PLUGIN_URL . 'assets/js/text-size-support.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
            TEXT_SIZE_ADJUST_VERSION,
            array(
                'in_footer' => true,
                'strategy' => 'defer'
            )
        );

        wp_set_script_translations('text-size-adjust-editor', 'text-size-adjust', TEXT_SIZE_ADJUST_PLUGIN_DIR . 'languages');
    }

    public function enqueue_editor_assets() {
        wp_enqueue_script('text-size-adjust-editor');
    }
}

function text_size_adjust_init() {
    return Text_Size_Adjust_Plugin::get_instance();
}

add_action('plugins_loaded', 'text_size_adjust_init');

register_uninstall_hook(__FILE__, 'text_size_adjust_uninstall');

function text_size_adjust_uninstall() {
    delete_option('text_size_adjust_settings');
}
