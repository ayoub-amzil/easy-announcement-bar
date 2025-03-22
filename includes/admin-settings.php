<?php
function easy_announcement_bar_add_admin_menu() {
    add_menu_page(
        'Announcement Bar',
        'Announcement',
        'manage_options',
        'announcement-bar',
        'easy_announcement_bar_render_settings_page',
        'dashicons-megaphone',
        30
    );
}
add_action('admin_menu', 'easy_announcement_bar_add_admin_menu');

function easy_announcement_bar_render_settings_page() {
    $options = get_option('easy_announcement_bar_settings');
    $pages = get_pages();
    ?>
    <div class="wrap">
        <h2>Announcement Bar Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('easy_announcement_bar_settings_group'); ?>
            <table class="form-table">
                <tr>
                    <th>Enable Bar</th>
                    <td>
                        <label>
                            <input type="checkbox" name="easy_announcement_bar_settings[enabled]" value="1" <?php checked(isset($options['enabled']) ? $options['enabled'] : 0, 1); ?>>
                            Enable announcement bar
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>Announcement Text</th>
                    <td>
                        <textarea name="easy_announcement_bar_settings[text]" rows="3" cols="50"><?php echo esc_textarea($options['text'] ?? ''); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>Text Color</th>
                    <td>
                        <input type="text" name="easy_announcement_bar_settings[text_color]" value="<?php echo esc_attr($options['text_color'] ?? '#ffffff'); ?>" class="color-picker">
                    </td>
                </tr>
                <tr>
                    <th>Bar Color</th>
                    <td>
                        <input type="text" name="easy_announcement_bar_settings[bar_color]" value="<?php echo esc_attr($options['bar_color'] ?? '#000000'); ?>" class="color-picker">
                    </td>
                </tr>
                <tr>
                    <th>Text Speed (seconds)</th>
                    <td>
                        <input type="number" name="easy_announcement_bar_settings[speed]" min="5" max="60" step="1" value="<?php echo esc_attr($options['speed'] ?? 15); ?>">
                    </td>
                </tr>
                <tr>
                    <th>Text Direction</th>
                    <td>
                        <select name="easy_announcement_bar_settings[direction]">
                            <option value="left" <?php selected($options['direction'] ?? 'left', 'left'); ?>>Right to Left</option>
                            <option value="right" <?php selected($options['direction'] ?? 'left', 'right'); ?>>Left to Right</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Bar Position</th>
                    <td>
                        <select name="easy_announcement_bar_settings[position]">
                            <option value="top" <?php selected($options['position'] ?? 'top', 'top'); ?>>Top of the Page</option>
                            <option value="bottom" <?php selected($options['position'] ?? 'top', 'bottom'); ?>>Bottom of the Page</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Auto-Close Timer (seconds)</th>
                    <td>
                        <input type="number" name="easy_announcement_bar_settings[timer]" min="0" step="1" value="<?php echo esc_attr($options['timer'] ?? 0); ?>">
                        <p class="description">Set to 0 to disable auto-close.</p>
                    </td>
                </tr>
                <tr>
                    <th>Show on Specific Pages</th>
                    <td>
                        <select name="easy_announcement_bar_settings[pages][]" multiple="multiple" style="width: 50%; height: 120px;">
                            <?php foreach ($pages as $page) : ?>
                                <option value="<?php echo esc_attr($page->ID); ?>" <?php selected(in_array($page->ID, $options['pages'] ?? []), true); ?>>
                                    <?php echo esc_html($page->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">Hold Ctrl/Cmd to select multiple pages. Leave empty to show on all pages.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}