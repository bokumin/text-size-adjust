=== Text Size Adjust ===
Contributors: bokumin
Tags: typography, font-size, responsive, text-size, editor
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Globally configure and manage text sizes across your WordPress site with desktop and mobile-specific settings.

== Description ==

Text Size Adjust is a powerful WordPress plugin that allows you to globally configure text sizes for your website. It provides separate configurations for desktop and mobile views, ensuring optimal readability across all devices.

= Key Features =

* Seven predefined text sizes (XXS, XS, S, M, L, XL, XXL)
* Separate desktop (769px and above) and mobile (768px and below) configurations
* Real-time preview in the settings panel
* Integration with the WordPress block editor
* Compatible with default WordPress font size classes
* Simple class-based implementation

= Usage =

1. In the Block Editor:
   * Select any text block
   * Use the "Text Size Settings" panel in the sidebar
   * Choose from available size options

2. In HTML/CSS:
   * Add the class `has-text-[size]` to any element
   * Available sizes: xxs, xs, s, m, l, xl, xxl
   * Example: `<p class="has-text-m">Medium text</p>`

= WordPress Font Size Compatibility =

The plugin automatically maps WordPress default font size classes to corresponding plugin sizes:

* has-small-font-size → has-text-s
* has-medium-font-size → has-text-m
* has-large-font-size → has-text-l
* has-x-large-font-size → has-text-xl
* has-xx-large-font-size → has-text-xxl

== Installation ==

1. Upload the `text-size-adjust` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Text Size Settings to configure your text sizes

== Frequently Asked Questions ==

= Can I use this with any theme? =

Yes, the plugin works with any WordPress theme that follows standard practices.

= Will this affect existing content? =

No, the plugin only affects content where you specifically use the text size classes or settings.

= Can I override the settings for specific elements? =

Yes, you can override the plugin's styles using more specific CSS selectors in your theme.

= How do I set different sizes for mobile and desktop? =

Navigate to Settings > Text Size Settings, where you'll find separate configuration sections for desktop and mobile views.

== Screenshots ==

1. Settings page with desktop and mobile configurations
2. Block editor integration
3. Front-end display example

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release of Text Size Adjust plugin.

== Default Size Values ==

Desktop (769px and above):
* XXS: 12px
* XS: 13px
* S: 14px
* M: 16px
* L: 18px
* XL: 24px
* XXL: 32px

Mobile (768px and below):
* XXS: 10px
* XS: 11px
* S: 12px
* M: 14px
* L: 16px
* XL: 18px
* XXL: 20px
