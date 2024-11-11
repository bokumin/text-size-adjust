<?php
class GTS_Frontend_Styles {
    private $options_name = 'global_text_size_settings';
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

    public function output_custom_styles() {
        $options = get_option($this->options_name);
        
        echo "<!-- Global Text Size Styles Start -->\n";
        echo "<style type='text/css'>\n";

	//styles of desktop
        echo "@media screen and (min-width: 769px) {\n";
        foreach ($this->sizes as $size) {
            $option_key = 'desktop_' . $size;
            $value = isset($options[$option_key]) && !empty($options[$option_key])
                ? intval($options[$option_key])
                : $this->get_default_size($size);
            
            echo "  .has-text-{$size} { font-size: {$value}px !important; }\n";
            
            $wp_class = array_search($size, $this->wp_size_mapping);
            if ($wp_class) {
                echo "  .{$wp_class} { font-size: {$value}px !important; }\n";
            }
        }
        echo "}\n";

        // style of mobile
        echo "@media screen and (max-width: 768px) {\n";
        foreach ($this->sizes as $size) {
            $option_key = 'mobile_' . $size;
            $value = isset($options[$option_key]) && !empty($options[$option_key])
                ? intval($options[$option_key])
                : $this->get_default_size($size, true);
            
            echo "  .has-text-{$size} { font-size: {$value}px !important; }\n";
            
            $wp_class = array_search($size, $this->wp_size_mapping);
            if ($wp_class) {
                echo "  .{$wp_class} { font-size: {$value}px !important; }\n";
            }
        }
        echo "}\n";
        
        echo "</style>\n";
        echo "<!-- Global Text Size Styles End -->\n";
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
