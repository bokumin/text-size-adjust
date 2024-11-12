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

    public function should_apply_styles($device = 'desktop') {
        $options = get_option($this->options_name);
        
        // デバイス設定が存在しない場合はデフォルトで true を返す
        if (!isset($options[$device]['pages'])) {
            return true;
        }

        $page_settings = $options[$device]['pages'];
        
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
        $options = get_option($this->options_name);
        
        // Desktop styles
        if ($this->should_apply_styles('desktop')) {
            $desktop_styles = array();
            foreach ($this->sizes as $size) {
                $value = isset($options['desktop']['sizes'][$size]) 
                    ? intval($options['desktop']['sizes'][$size])
                    : $this->get_default_size($size);
                
                /* translators: %1$s: text size class name (xxs, xs, etc), %2$d: font size in pixels */
                $style_format = esc_html__(
                    '.has-text-%1$s { font-size: %2$dpx !important; }',
                    'text-size-adjust'
                );
                
                $desktop_styles[] = sprintf(
                    $style_format,
                    esc_attr($size),
                    absint($value)
                );
                
                $wp_class = array_search($size, $this->wp_size_mapping);
                if ($wp_class) {
                    /* translators: %1$s: WordPress font size class name, %2$d: font size in pixels */
                    $wp_style_format = esc_html__(
                        '.%1$s { font-size: %2$dpx !important; }',
                        'text-size-adjust'
                    );
                    
                    $desktop_styles[] = sprintf(
                        $wp_style_format,
                        esc_attr($wp_class),
                        absint($value)
                    );
                }
            }
        }
        
        // Mobile styles
        if ($this->should_apply_styles('mobile')) {
            $mobile_styles = array();
            foreach ($this->sizes as $size) {
                $value = isset($options['mobile']['sizes'][$size])
                    ? intval($options['mobile']['sizes'][$size])
                    : $this->get_default_size($size, true);
                
                /* translators: %1$s: text size class name (xxs, xs, etc), %2$d: font size in pixels */
                $style_format = esc_html__(
                    '.has-text-%1$s { font-size: %2$dpx !important; }',
                    'text-size-adjust'
                );
                
                $mobile_styles[] = sprintf(
                    $style_format,
                    esc_attr($size),
                    absint($value)
                );
                
                $wp_class = array_search($size, $this->wp_size_mapping);
                if ($wp_class) {
                    /* translators: %1$s: WordPress font size class name, %2$d: font size in pixels */
                    $wp_style_format = esc_html__(
                        '.%1$s { font-size: %2$dpx !important; }',
                        'text-size-adjust'
                    );
                    
                    $mobile_styles[] = sprintf(
                        $wp_style_format,
                        esc_attr($wp_class),
                        absint($value)
                    );
                }
            }
        }

        // Prepare and output the styles
        if (!empty($desktop_styles) || !empty($mobile_styles)) {
            ?>
            <style type="text/css">
                <?php if (!empty($desktop_styles)): ?>
                @media screen and (min-width: 769px) {
                    <?php echo esc_html(implode("\n    ", $desktop_styles)); ?>
                }
                <?php endif; ?>
                
                <?php if (!empty($mobile_styles)): ?>
                @media screen and (max-width: 768px) {
                    <?php echo esc_html(implode("\n    ", $mobile_styles)); ?>
                }
                <?php endif; ?>
            </style>
            <?php
        }
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
