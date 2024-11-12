<?php
class GTS_Admin_Settings {
    private $options_name = 'text_size_adjust_settings';
    private $sizes = array('xxs', 'xs', 's', 'm', 'l', 'xl', 'xxl');
    private $page_options = array(
        'home' => 'Homepage',
        'posts' => 'Posts/Articles',
        'pages' => 'Pages',
        'archives' => 'Archive Pages',
        'search' => 'Search Results',
        'error404' => '404 Page'
    );
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function enqueue_admin_assets($hook) {
        if ('settings_page_text-size-adjust' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'text-size-adjust-admin',
            GTS_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            GTS_VERSION
        );

        wp_enqueue_script(
            'text-size-adjust-admin',
            GTS_PLUGIN_URL . 'assets/js/admin-settings.js',
            array('jquery', 'jquery-ui-tabs'),
            GTS_VERSION,
            true
        );
        
        // カスタムスタイルをインラインで追加
        $custom_css = "
            #text-size-tabs > ul {
                display: flex;
                gap: 10px;
                border: none;
                background: none;
                padding: 0;
                margin-bottom: 20px;
            }
            #text-size-tabs > ul::before {
                display: none;
            }
            #text-size-tabs > ul li {
                margin: 0;
                padding: 0;
                border: none;
                background: none;
                float: none;
            }
            #text-size-tabs > ul li a {
                display: inline-block;
                padding: 10px 20px;
                background: #f0f0f1;
                border: 1px solid #c3c4c7;
                border-radius: 4px;
                text-decoration: none;
                color: #50575e;
                font-weight: 500;
                transition: all 0.3s ease;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }
            #text-size-tabs > ul li a:hover {
                background: #fff;
                border-color: #8c8f94;
                color: #1d2327;
            }
            #text-size-tabs > ul li.ui-state-active a {
                background: #2271b1;
                border-color: #2271b1;
                color: #fff;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            }
            #text-size-tabs > ul li.ui-state-active a:hover {
                background: #135e96;
                border-color: #135e96;
            }
            #text-size-tabs .ui-tabs-panel {
                padding: 20px;
                background: #fff;
                border: 1px solid #c3c4c7;
                border-radius: 4px;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }
        ";
        wp_add_inline_style('text-size-adjust-admin', $custom_css);
    }

    public function add_settings_page() {
        add_options_page(
            esc_html__('Text Size Settings', 'text-size-adjust'),
            esc_html__('Text Size Settings', 'text-size-adjust'),
            'manage_options',
            'text-size-adjust',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting($this->options_name, $this->options_name, array(
            'sanitize_callback' => array($this, 'sanitize_settings')
        ));

        // Desktop settings sections
        add_settings_section(
            'desktop_page_settings',
            esc_html__('Desktop Page Settings', 'text-size-adjust'),
            array($this, 'render_page_settings_description'),
            'text-size-adjust-desktop'
        );

        add_settings_section(
            'desktop_settings',
            esc_html__('Desktop Font Sizes', 'text-size-adjust'),
            array($this, 'render_desktop_description'),
            'text-size-adjust-desktop'
        );

        // Mobile settings sections
        add_settings_section(
            'mobile_page_settings',
            esc_html__('Mobile Page Settings', 'text-size-adjust'),
            array($this, 'render_page_settings_description'),
            'text-size-adjust-mobile'
        );

        add_settings_section(
            'mobile_settings',
            esc_html__('Mobile Font Sizes', 'text-size-adjust'),
            array($this, 'render_mobile_description'),
            'text-size-adjust-mobile'
        );

        // Register desktop page settings fields
        foreach ($this->page_options as $key => $label) {
            add_settings_field(
                'desktop_page_' . $key,
                esc_html($label),
                array($this, 'render_page_field'),
                'text-size-adjust-desktop',
                'desktop_page_settings',
                array(
                    'label_for' => 'desktop_page_' . $key,
                    'key' => $key,
                    'device' => 'desktop'
                )
            );
        }

        // Register mobile page settings fields
        foreach ($this->page_options as $key => $label) {
            add_settings_field(
                'mobile_page_' . $key,
                esc_html($label),
                array($this, 'render_page_field'),
                'text-size-adjust-mobile',
                'mobile_page_settings',
                array(
                    'label_for' => 'mobile_page_' . $key,
                    'key' => $key,
                    'device' => 'mobile'
                )
            );
        }

        // Register size fields for desktop
        foreach ($this->sizes as $size) {
            /* translators: %s: Size identifier (XXS, XS, S, M, L, XL, XXL) */
            $label = sprintf(esc_html__('Size %s', 'text-size-adjust'), strtoupper($size));
            
            add_settings_field(
                'desktop_' . $size,
                $label,
                array($this, 'render_size_field'),
                'text-size-adjust-desktop',
                'desktop_settings',
                array(
                    'label_for' => 'desktop_' . $size,
                    'default' => $this->get_default_size($size),
                    'size' => $size,
                    'device' => 'desktop'
                )
            );
        }

        // Register size fields for mobile
        foreach ($this->sizes as $size) {
            /* translators: %s: Size identifier (XXS, XS, S, M, L, XL, XXL) */
            $label = sprintf(esc_html__('Size %s', 'text-size-adjust'), strtoupper($size));
            
            add_settings_field(
                'mobile_' . $size,
                $label,
                array($this, 'render_size_field'),
                'text-size-adjust-mobile',
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

    public function render_page_settings_description() {
        echo wp_kses_post('<p class="description">' . 
            esc_html__('Select which pages should have the text size adjustments applied.', 'text-size-adjust') . 
            '</p>'
        );
    }

    public function render_page_field($args) {
        $options = get_option($this->options_name);
        $key = $args['key'];
        $device = $args['device'];
        $field_id = $device . '_page_' . $key;
        $checked = isset($options[$device]['pages'][$key]) ? $options[$device]['pages'][$key] : true;

        printf(
            '<input type="checkbox" 
                id="%1$s" 
                name="%2$s[%3$s][pages][%4$s]" 
                value="1" 
                %5$s
            />',
            esc_attr($field_id),
            esc_attr($this->options_name),
            esc_attr($device),
            esc_attr($key),
            checked($checked, true, false)
        );
    }

    public function render_desktop_description() {
        echo wp_kses_post('<p class="description">' . 
            esc_html__('Set font sizes for desktop display (769px and above).', 'text-size-adjust') . 
            '</p>'
        );
    }

    public function render_mobile_description() {
        echo wp_kses_post('<p class="description">' . 
            esc_html__('Set font sizes for mobile display (768px and below).', 'text-size-adjust') . 
            '</p>'
        );
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
        return isset($defaults[$size]) ? $defaults[$size] : 16;
    }

    public function render_size_field($args) {
        $options = get_option($this->options_name);
        $device = $args['device'];
        $size = $args['size'];
        $field_id = $device . '_' . $size;
        
        $value = isset($options[$device]['sizes'][$size]) 
            ? $options[$device]['sizes'][$size] 
            : $args['default'];
        
        $preview_text = esc_html__('ABCDEFGabcdefg123456', 'text-size-adjust');
        
        echo '<div class="size-field-container">';
        
        echo '<div class="size-input-wrapper">';
        printf(
            '<input type="number" 
                id="%1$s" 
                name="%2$s[%3$s][sizes][%4$s]" 
                value="%5$s" 
                min="8" 
                max="100" 
                class="size-input" 
                data-size="%4$s"
                data-device="%3$s"
            /> %6$s',
            esc_attr($field_id),
            esc_attr($this->options_name),
            esc_attr($device),
            esc_attr($size),
            esc_attr($value),
            esc_html__('px', 'text-size-adjust')
        );
        echo '</div>';

        printf(
            '<div class="size-preview preview-%1$s" style="font-size: %2$spx">
                <span class="preview-text">%3$s</span>
                <span class="preview-size">%2$spx</span>
            </div>',
            esc_attr($size),
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
        <div class="wrap text-size-adjust-settings">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="settings-header">
                <div class="settings-description">
                    <p><?php esc_html_e('This plugin allows you to configure text sizes that can be used throughout your site.', 'text-size-adjust'); ?></p>
                    <p><?php esc_html_e('You can set different sizes for desktop and mobile displays, and choose which pages to apply these settings to.', 'text-size-adjust'); ?></p>
                </div>
                
                <div class="settings-tips">
                    <h3><?php esc_html_e('Usage Tips', 'text-size-adjust'); ?></h3>
                    <ul>
                        <li><?php esc_html_e('Select text in the block editor and choose a size from the "Text Size Settings" in the sidebar.', 'text-size-adjust'); ?></li>
                        <li><?php esc_html_e('You can also add the HTML class "has-text-[size]" to specify the size.', 'text-size-adjust'); ?></li>
                        <li><?php esc_html_e('Available sizes are: xxs, xs, s, m, l, xl, xxl.', 'text-size-adjust'); ?></li>
                    </ul>
                </div>
            </div>

            <form action="options.php" method="post">
                <?php settings_fields($this->options_name); ?>
                
                <div id="text-size-tabs">
                    <ul>
                        <li><a href="#desktop-tab"><?php esc_html_e('Desktop Settings', 'text-size-adjust'); ?></a></li>
                        <li><a href="#mobile-tab"><?php esc_html_e('Mobile Settings', 'text-size-adjust'); ?></a></li>
                    </ul>
                    
                    <div id="desktop-tab">
                        <?php do_settings_sections('text-size-adjust-desktop'); ?>
                    </div>
                    
                    <div id="mobile-tab">
                        <?php do_settings_sections('text-size-adjust-mobile'); ?>
                    </div>
                </div>

                <?php submit_button(esc_html__('Save Settings', 'text-size-adjust')); ?>
            </form>
        </div>
        <?php
    }

    public function sanitize_settings($input) {
        $sanitized_input = array();
        
        // Sanitize desktop settings
        if (isset($input['desktop'])) {
            $sanitized_input['desktop'] = array();
            
            // Sanitize desktop page settings
            if (isset($input['desktop']['pages'])) {
                $sanitized_input['desktop']['pages'] = array();
                foreach ($this->page_options as $key => $label) {
$sanitized_input['desktop']['pages'][$key] = isset($input['desktop']['pages'][$key]) ? true : false;
                }
            }
            
            // Sanitize desktop size settings
            if (isset($input['desktop']['sizes'])) {
                $sanitized_input['desktop']['sizes'] = array();
                foreach ($this->sizes as $size) {
                    if (isset($input['desktop']['sizes'][$size])) {
                        $sanitized_input['desktop']['sizes'][$size] = absint($input['desktop']['sizes'][$size]);
                    }
                }
            }
        }
        
        // Sanitize mobile settings
        if (isset($input['mobile'])) {
            $sanitized_input['mobile'] = array();
            
            // Sanitize mobile page settings
            if (isset($input['mobile']['pages'])) {
                $sanitized_input['mobile']['pages'] = array();
                foreach ($this->page_options as $key => $label) {
                    $sanitized_input['mobile']['pages'][$key] = isset($input['mobile']['pages'][$key]) ? true : false;
                }
            }
            
            // Sanitize mobile size settings
            if (isset($input['mobile']['sizes'])) {
                $sanitized_input['mobile']['sizes'] = array();
                foreach ($this->sizes as $size) {
                    if (isset($input['mobile']['sizes'][$size])) {
                        $sanitized_input['mobile']['sizes'][$size] = absint($input['mobile']['sizes'][$size]);
                    }
                }
            }
        }

        return $sanitized_input;
    }
}
