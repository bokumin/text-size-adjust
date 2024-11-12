<?php
class GTS_Frontend_Styles {
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
        add_action('wp_head', array($this, 'output_custom_styles'), 100);
    }

    public function should_apply_styles() {
        $options = get_option($this->options_name);
        $page_settings = isset($options['pages']) ? $options['pages'] : array();
        
        // デフォルトで全てのページに適用
        if (empty($page_settings)) {
            return true;
        }

        // 各ページタイプをチェック
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

    public function output_custom_styles() {
        if (!$this->should_apply_styles()) {
            return;
        }

        $options = get_option($this->options_name);
        $styles = array();
        
        // Desktop styles
        $desktop_styles = array();
        foreach ($this->sizes as $size) {
            $option_key = 'desktop_' . $size;
            $value = isset($options[$option_key]) && !empty($options[$option_key])
                ? intval($options[$option_key])
                : $this->get_default_size($size);
            
            $desktop_styles[] = sprintf(
                '.has-text-%1$s { font-size: %2$dpx !important; }',
                esc_attr($size),
                absint($value)
            );
            
            $wp_class = array_search($size, $this->wp_size_mapping);
            if ($wp_class) {
                $desktop_styles[] = sprintf(
                    '.%1$s { font-size: %2$dpx !important; }',
                    esc_attr($wp_class),
                    absint($value)
                );
            }
        }
        
        // Mobile styles
        $mobile_styles = array();
        foreach ($this->sizes as $size) {
            $option_key = 'mobile_' . $size;
            $value = isset($options[$option_key]) && !empty($options[$option_key])
                ? intval($options[$option_key])
                : $this->get_default_size($size, true);
            
            $mobile_styles[] = sprintf(
                '.has-text-%1$s { font-size: %2$dpx !important; }',
                esc_attr($size),
                absint($value)
            );
            
            $wp_class = array_search($size, $this->wp_size_mapping);
            if ($wp_class) {
                $mobile_styles[] = sprintf(
                    '.%1$s { font-size: %2$dpx !important; }',
                    esc_attr($wp_class),
                    absint($value)
                );
            }
        }

        // Prepare the styles
        $desktop_styles = implode("\n    ", $desktop_styles);
        $mobile_styles = implode("\n    ", $mobile_styles);

        // Output the styles safely
        ?>
        <style type="text/css">
            @media screen and (min-width: 769px) {
                <?php echo esc_html(wp_strip_all_tags($desktop_styles)); ?>
            }
            @media screen and (max-width: 768px) {
                <?php echo esc_html(wp_strip_all_tags($mobile_styles)); ?>
            }
        </style>
        <?php
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
}
