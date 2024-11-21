<?php
<?php
class Text_Size_Adjust_Frontend_Styles {
    private $options_name = 'text_size_adjust_settings';
    private $sizes = array('xxs', 'xs', 's', 'm', 'l', 'xl', 'xxl');
    private $wp_size_mapping = array(
        'has-small-font-size' => 's',
        'has-medium-font-size' => 'm',
        'has-large-font-size' => 'l',
        'has-x-large-font-size' => 'xl',
        'has-xx-large-font-size' => 'xxl'
    );

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_styles'));
    }

    /**
     * Enqueues the frontend styles and adds custom inline styles
     */
    public function enqueue_frontend_styles() {
        if (!$this->should_apply_styles('desktop') && !$this->should_apply_styles('mobile')) {
            return;
        }

        // Register and enqueue the base stylesheet
        wp_register_style(
            'text-size-adjust-frontend',
            TEXT_SIZE_ADJUST_PLUGIN_URL . 'assets/css/frontend-style.css',
            array(),
            TEXT_SIZE_ADJUST_VERSION
        );
        wp_enqueue_style('text-size-adjust-frontend');

        // Add custom inline styles
        $custom_styles = $this->generate_custom_styles();
        if ($custom_styles) {
            wp_add_inline_style('text-size-adjust-frontend', $custom_styles);
        }
    }

    /**
     * Determines if styles should be applied based on current page and device settings
     *
     * @param string $device Device type ('desktop' or 'mobile')
     * @return boolean
     */
    public function should_apply_styles($device = 'desktop') {
        $options = get_option($this->options_name);
        
        // Default to true if device settings don't exist
        if (!isset($options[$device]['pages'])) {
            return true;
        }

        $page_settings = $options[$device]['pages'];
        
        // Check each page type
        if (is_front_page() && isset($page_settings['home']) && $page_settings['home']) {
            return true;
        }
        if (is_single() && isset($page_settings['posts']) && $page_settings['posts']) {
            return true;
        }
        if (is_page() && isset($page_settings['pages']) && $page_settings['pages']) {
            return true;
        }
        if (is_archive() && isset($page_settings['archives']) && $page_settings['archives']) {
            return true;
        }
        if (is_search() && isset($page_settings['search']) && $page_settings['search']) {
            return true;
        }
        if (is_404() && isset($page_settings['error404']) && $page_settings['error404']) {
            return true;
        }

        return false;
    }

    /**
     * Generates custom CSS styles based on settings
     *
     * @return string Generated CSS
     */
    private function generate_custom_styles() {
        $options = get_option($this->options_name);
        $styles = array();
        
        // Generate desktop styles
        if ($this->should_apply_styles('desktop')) {
            $desktop_styles = array();
            foreach ($this->sizes as $size) {
                $value = isset($options['desktop']['sizes'][$size]) 
                    ? intval($options['desktop']['sizes'][$size])
                    : $this->get_default_size($size);
                
                $desktop_styles[] = sprintf(
                    '.has-text-%s { font-size: %dpx !important; }',
                    esc_attr($size),
                    absint($value)
                );
                
                // Add WordPress core font size class mappings
                $wp_class = array_search($size, $this->wp_size_mapping);
                if ($wp_class) {
                    $desktop_styles[] = sprintf(
                        '.%s { font-size: %dpx !important; }',
                        esc_attr($wp_class),
                        absint($value)
                    );
                }
            }
            
            if (!empty($desktop_styles)) {
                $styles[] = '@media screen and (min-width: 769px) {';
                $styles[] = implode("\n", $desktop_styles);
                $styles[] = '}';
            }
        }
        
        // Generate mobile styles
        if ($this->should_apply_styles('mobile')) {
            $mobile_styles = array();
            foreach ($this->sizes as $size) {
                $value = isset($options['mobile']['sizes'][$size])
                    ? intval($options['mobile']['sizes'][$size])
                    : $this->get_default_size($size, true);
                
                $mobile_styles[] = sprintf(
                    '.has-text-%s { font-size: %dpx !important; }',
                    esc_attr($size),
                    absint($value)
                );
                
                // Add WordPress core font size class mappings
                $wp_class = array_search($size, $this->wp_size_mapping);
                if ($wp_class) {
                    $mobile_styles[] = sprintf(
                        '.%s { font-size: %dpx !important; }',
                        esc_attr($wp_class),
                        absint($value)
                    );
                }
            }
            
            if (!empty($mobile_styles)) {
                $styles[] = '@media screen and (max-width: 768px) {';
                $styles[] = implode("\n", $mobile_styles);
                $styles[] = '}';
            }
        }

        return implode("\n", $styles);
    }

    /**
     * Gets the default size for a given size identifier
     *
     * @param string $size Size identifier
     * @param boolean $is_mobile Whether to get mobile defaults
     * @return integer Default size in pixels
     */
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

    /**
     * Validates and sanitizes a font size value
     *
     * @param integer $size Font size in pixels
     * @return integer Sanitized font size
     */
    private function sanitize_font_size($size) {
        $size = absint($size);
        return max(8, min(100, $size)); // Ensure size is between 8 and 100 pixels
    }

    /**
     * Gets all available size options with their current values
     *
     * @param string $device Device type ('desktop' or 'mobile')
     * @return array Array of size options and their values
     */
    public function get_size_options($device = 'desktop') {
        $options = get_option($this->options_name);
        $size_options = array();
        
        foreach ($this->sizes as $size) {
            $size_options[$size] = isset($options[$device]['sizes'][$size])
                ? $this->sanitize_font_size($options[$device]['sizes'][$size])
                : $this->get_default_size($size, $device === 'mobile');
        }
        
        return $size_options;
    }

    /**
     * Updates font size settings for a specific device
     *
     * @param array $sizes Array of sizes and their values
     * @param string $device Device type ('desktop' or 'mobile')
     * @return boolean Whether the update was successful
     */
    public function update_size_settings($sizes, $device = 'desktop') {
        $options = get_option($this->options_name, array());
        
        if (!isset($options[$device])) {
            $options[$device] = array();
        }
        
        $options[$device]['sizes'] = array();
        foreach ($this->sizes as $size) {
            if (isset($sizes[$size])) {
                $options[$device]['sizes'][$size] = $this->sanitize_font_size($sizes[$size]);
            } else {
                $options[$device]['sizes'][$size] = $this->get_default_size($size, $device === 'mobile');
            }
        }
        
        return update_option($this->options_name, $options);
    }

    /**
     * Reset font sizes to their default values
     *
     * @param string $device Device type ('desktop' or 'mobile')
     * @return boolean Whether the reset was successful
     */
    public function reset_to_defaults($device = 'desktop') {
        $options = get_option($this->options_name, array());
        
        if (!isset($options[$device])) {
            $options[$device] = array();
        }
        
        $options[$device]['sizes'] = array();
        foreach ($this->sizes as $size) {
            $options[$device]['sizes'][$size] = $this->get_default_size($size, $device === 'mobile');
        }
        
        return update_option($this->options_name, $options);
    }
}
