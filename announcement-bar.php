<?php
/**
 * Plugin Name: Easy Announcement Bar
 * Plugin URI: https://wordpress.org/plugins/easy-announcement-bar
 * Description: Easy Announcement Bar plugin adds a customizable, scrolling announcement bar to your WordPress site. Display important messages, promotions, or updates with ease!
 * Version: 1.0.0
 * Author: AMZIL AYOUB
 * Author URI: https://www.linkedin.com/in/amzil-ayoub/
 * Text Domain: easy-announcement-bar
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

if (!defined('ABSPATH')) exit;

define('EASY_ANNOUNCEMENT_BAR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EASY_ANNOUNCEMENT_BAR_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once EASY_ANNOUNCEMENT_BAR_PLUGIN_DIR . 'includes/admin-settings.php';

function easy_announcement_bar_register_settings() {
    register_setting('easy_announcement_bar_settings_group', 'easy_announcement_bar_settings', 'easy_announcement_bar_sanitize_settings');
}

function easy_announcement_bar_sanitize_settings($input) {
    $sanitized_input = array();

    if (isset($input['enabled'])) {
        $sanitized_input['enabled'] = absint($input['enabled']);
    }

    if (isset($input['text'])) {
        $sanitized_input['text'] = sanitize_text_field($input['text']);
    }

    if (isset($input['text_color'])) {
        $sanitized_input['text_color'] = sanitize_hex_color($input['text_color']);
    }

    if (isset($input['bar_color'])) {
        $sanitized_input['bar_color'] = sanitize_hex_color($input['bar_color']);
    }

    if (isset($input['speed'])) {
        $sanitized_input['speed'] = absint($input['speed']);
    }

    if (isset($input['direction'])) {
        $sanitized_input['direction'] = in_array($input['direction'], array('left', 'right')) ? $input['direction'] : 'left';
    }

    if (isset($input['position'])) {
        $sanitized_input['position'] = in_array($input['position'], array('top', 'bottom')) ? $input['position'] : 'top';
    }

    if (isset($input['timer'])) {
        $sanitized_input['timer'] = absint($input['timer']);
    }

    if (isset($input['pages'])) {
        $sanitized_input['pages'] = array_map('absint', $input['pages']);
    }

    return $sanitized_input;
}
add_action('admin_init', 'easy_announcement_bar_register_settings');

function easy_announcement_bar_admin_assets($hook) {
    if ($hook === 'toplevel_page_announcement-bar') {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('ab-admin-css', EASY_ANNOUNCEMENT_BAR_PLUGIN_URL . 'assets/css/admin.css', array(), filemtime(plugin_dir_path(__FILE__) . 'assets/css/admin.css'));
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('ab-admin-js', EASY_ANNOUNCEMENT_BAR_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'wp-color-picker'), filemtime(plugin_dir_path(__FILE__) . 'assets/js/admin.js'), true);
    }
}
add_action('admin_enqueue_scripts', 'easy_announcement_bar_admin_assets');

function easy_announcement_bar_frontend_assets() {
    $options = get_option('easy_announcement_bar_settings');
    if (empty($options['enabled']) || empty($options['text'])) return;

    wp_enqueue_style('ab-frontend-css', EASY_ANNOUNCEMENT_BAR_PLUGIN_URL . 'assets/css/frontend.css', array(), filemtime(plugin_dir_path(__FILE__) . 'assets/css/frontend.css'));
    wp_enqueue_script('ab-frontend-js', EASY_ANNOUNCEMENT_BAR_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), filemtime(plugin_dir_path(__FILE__) . 'assets/js/frontend.js'), true);
}
add_action('wp_enqueue_scripts', 'easy_announcement_bar_frontend_assets');

function easy_announcement_bar_display_bar() {
    $options = get_option('easy_announcement_bar_settings');
    if (empty($options['enabled']) || empty($options['text'])) return;

    $pages = $options['pages'] ?? [];
    if (!empty($pages)) {
        $current_page_id = null;

        if (is_front_page() || is_home()) {
            $current_page_id = get_option('page_on_front');
        }
        elseif (is_singular()) {
            $current_page_id = get_the_ID();
        }
        elseif (function_exists('is_shop') && is_shop()) {
            $current_page_id = wc_get_page_id('shop');
        }
        elseif (function_exists('is_cart') && is_cart()) {
            $current_page_id = wc_get_page_id('cart');
        }
        elseif (function_exists('is_checkout') && is_checkout()) {
            $current_page_id = wc_get_page_id('checkout');
        }
        elseif (function_exists('is_account_page') && is_account_page()) {
            $current_page_id = wc_get_page_id('myaccount');
        }
        elseif (is_post_type_archive('product') || is_tax('product_cat') || is_tax('product_tag')) {
            return;
        }
        elseif (is_home() && !is_front_page()) {
            $current_page_id = get_option('page_for_posts');
        }

        if (!$current_page_id || !in_array($current_page_id, $pages)) {
            return;
        }
    }

    $direction = $options['direction'] === 'right' ? 'marquee-right' : 'marquee-left';
    $speed = isset($options['speed']) ? absint($options['speed']) : 15;
    $position = isset($options['position']) ? $options['position'] : 'top';
    $timer = isset($options['timer']) ? absint($options['timer']) : 0;
    ?>
    <div class="announcement-bar <?php echo esc_attr($position); ?>" style="background: <?php echo esc_attr($options['bar_color']); ?>;" data-timer="<?php echo esc_attr($timer); ?>">
        <div class="marquee-container">
            <div class="marquee <?php echo esc_attr($direction); ?>"
                 style="color: <?php echo esc_attr($options['text_color']); ?>;
                         animation-duration: <?php echo esc_attr($speed); ?>s;">
                <?php echo esc_html($options['text']); ?>
            </div>
        </div>
        <button class="ab-close-button" aria-label="Close announcement bar">&#10006;</button>
    </div>
    <?php
}
add_action('wp_body_open', 'easy_announcement_bar_display_bar');