<?php
class GTS_Admin_Settings {
    private $options_name = 'global_text_size_settings';
    private $sizes = array('xxs', 'xs', 's', 'm', 'l', 'xl', 'xxl');
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function enqueue_admin_assets($hook) {
        if ('settings_page_global-text-size' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'global-text-size-admin',
            GTS_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            GTS_VERSION
        );

        wp_enqueue_script(
            'global-text-size-admin',
            GTS_PLUGIN_URL . 'assets/js/admin-settings.js',
            array('jquery'),
            GTS_VERSION,
            true
        );
    }

    public function add_settings_page() {
        add_options_page(
            __('テキストサイズ設定', 'global-text-size'),
            __('テキストサイズ設定', 'global-text-size'),
            'manage_options',
            'global-text-size',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting($this->options_name, $this->options_name, array(
            'sanitize_callback' => array($this, 'sanitize_settings')
        ));

        // デスクトップ設定セクション
        add_settings_section(
            'desktop_settings',
            __('デスクトップ設定', 'global-text-size'),
            array($this, 'render_desktop_description'),
            'global-text-size'
        );

        // モバイル設定セクション
        add_settings_section(
            'mobile_settings',
            __('モバイル設定', 'global-text-size'),
            array($this, 'render_mobile_description'),
            'global-text-size'
        );

        // 各サイズの設定フィールドを追加
        foreach ($this->sizes as $size) {
            // デスクトップ設定
            add_settings_field(
                'desktop_' . $size,
                sprintf(__('サイズ %s', 'global-text-size'), strtoupper($size)),
                array($this, 'render_size_field'),
                'global-text-size',
                'desktop_settings',
                array(
                    'label_for' => 'desktop_' . $size,
                    'default' => $this->get_default_size($size),
                    'size' => $size,
                    'device' => 'desktop'
                )
            );

            // モバイル設定
            add_settings_field(
                'mobile_' . $size,
                sprintf(__('サイズ %s', 'global-text-size'), strtoupper($size)),
                array($this, 'render_size_field'),
                'global-text-size',
                'mobile_settings',
                array(
                    'label_for' => 'mobile_' . $size,
                    'default' => $this->get_default_size($size, true),
                    'size' => $size,
                    'device' => 'mobile'
                )
            );
        }
    }

    public function render_desktop_description() {
        echo '<p class="description">' . __('Set font sizes for desktop display (769px and above).', 'global-text-size') . '</p>';
    }

    public function render_mobile_description() {
        echo '<p class="description">' . __('Set font sizes for mobile display (768px and below).', 'global-text-size') . '</p>';
    }

    private function get_default_size($size, $is_mobile = false) {
        $defaults = array(
            'xxs' => $is_mobile ? 10 : 12,
            'xs'  => $is_mobile ? 11 : 13,
            's'   => $is_mobile ? 12 : 14,
            'm'   => $is_mobile ? 14 : 16,
            'l'   => $is_mobile ? 16 : 18,
            'xl'  => $is_mobile ? 18 : 24,
            'xxl' => $is_mobile ? 20 : 32
        );
        return $defaults[$size];
    }

    public function render_size_field($args) {
        $options = get_option($this->options_name);
        $value = isset($options[$args['label_for']]) 
            ? $options[$args['label_for']] 
            : $args['default'];
        
        $preview_text = __('ABCDEFGabcdefg123456', 'global-text-size');
        
        echo '<div class="size-field-container">';
        
        echo '<div class="size-input-wrapper">';
        printf(
            '<input type="number" 
                id="%1$s" 
                name="%2$s[%1$s]" 
                value="%3$s" 
                min="8" 
                max="100" 
                class="size-input" 
                data-size="%4$s"
                data-device="%5$s"
            /> px',
            esc_attr($args['label_for']),
            esc_attr($this->options_name),
            esc_attr($value),
            esc_attr($args['size']),
            esc_attr($args['device'])
        );
        echo '</div>';

        // プレビュー表示
        printf(
            '<div class="size-preview preview-%1$s" style="font-size: %2$spx">
                <span class="preview-text">%3$s</span>
                <span class="preview-size">%2$spx</span>
            </div>',
            esc_attr($args['size']),
            esc_attr($value),
            esc_html($preview_text)
        );
        
        echo '</div>';
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap global-text-size-settings">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="settings-header">
                <div class="settings-description">
                    <p><?php _e('This plugin allows you to configure text sizes that can be used throughout your site.', 'global-text-size'); ?></p>
                    <p><?php _e('You can set different sizes for desktop and mobile displays.', 'global-text-size'); ?></p>
                </div>
                
                <div class="settings-tips">
                    <h3><?php _e('Usage Tips', 'global-text-size'); ?></h3>
                    <ul>
                        <li><?php _e('Select text in the block editor and choose a size from the "Text Size Settings" in the sidebar.', 'global-text-size'); ?></li>
                        <li><?php _e('You can also add the HTML class "has-text-[size]" to specify the size.', 'global-text-size'); ?></li>
                        <li><?php _e('Available sizes are: xxs, xs, s, m, l, xl, xxl.', 'global-text-size'); ?></li>
                    </ul>
                </div>
            </div>

            <form action="options.php" method="post">
                <?php
                settings_fields($this->options_name);
                do_settings_sections('global-text-size');
                submit_button(__('Save Settings', 'global-text-size'));
                ?>
            </form>
        </div>
        <?php
    }

    public function sanitize_settings($input) {
        $sanitized_input = array();
        foreach ($input as $key => $value) {
            $sanitized_input[$key] = absint($value);
        }
        return $sanitized_input;
    }
}
