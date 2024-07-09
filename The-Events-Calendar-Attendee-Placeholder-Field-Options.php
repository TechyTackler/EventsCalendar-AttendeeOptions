<?php
/*
Plugin Name: The Events Calendar Attendee Placeholder Field Options
Description: Allows custom placeholder text and colour options for The Events Calendar attendee ticket fields.
Version: 1.3
Author: Simon Brown - weSTART Digital Agency
Author URI: https://westart.agency
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.7
Tested up to: 6.5
Requires PHP: 7.4

Changelog:
Version 1.3:
- Fixed issue with name placeholder not changing.
- Added admin menu icon.

Version 1.2:
- Added option to set the non-focus border size in pixels.
- Added option to set the focus border size in pixels.

Version 1.1:
- Added option to set placeholder text colour.
- Added option to set focus border colour.

Version 1.0:
- Initial release with placeholder text customization options.
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add settings menu
function custom_placeholder_menu() {
    add_menu_page(
        'Placeholder Field Options',
        'Events Placeholder Settings',
        'manage_options',
        'custom-placeholder-text',
        'custom_placeholder_settings_page',
        'dashicons-edit',
        5  // This number sets the position. Lower numbers are higher up in the menu.
    );
}
add_action('admin_menu', 'custom_placeholder_menu');

// Register settings
function custom_placeholder_settings_init() {
    register_setting('customPlaceholder', 'custom_placeholder_email');
    register_setting('customPlaceholder', 'custom_placeholder_name');
    register_setting('customPlaceholder', 'custom_placeholder_border_colour');
    register_setting('customPlaceholder', 'custom_placeholder_border_focus_colour');
    register_setting('customPlaceholder', 'custom_placeholder_initial_text_colour');
    register_setting('customPlaceholder', 'custom_placeholder_border_focus_size');
    register_setting('customPlaceholder', 'custom_placeholder_border_size');

    add_settings_section(
        'custom_placeholder_section',
        'Custom Placeholder Text Settings',
        'custom_placeholder_section_callback',
        'custom-placeholder-text'
    );

    add_settings_field(
        'custom_placeholder_email',
        'Email Field Placeholder',
        'custom_placeholder_email_render',
        'custom-placeholder-text',
        'custom_placeholder_section'
    );

    add_settings_field(
        'custom_placeholder_name',
        'Name Field Placeholder',
        'custom_placeholder_name_render',
        'custom-placeholder-text',
        'custom_placeholder_section'
    );

    add_settings_field(
        'custom_placeholder_border_colour',
        'Placeholder Border Colour',
        'custom_placeholder_border_colour_render',
        'custom-placeholder-text',
        'custom_placeholder_section'
    );

    add_settings_field(
        'custom_placeholder_border_focus_colour',
        'Placeholder Border Focus Colour',
        'custom_placeholder_border_focus_colour_render',
        'custom-placeholder-text',
        'custom_placeholder_section'
    );

    add_settings_field(
        'custom_placeholder_initial_text_colour',
        'Initial Placeholder Text Colour',
        'custom_placeholder_initial_text_colour_render',
        'custom-placeholder-text',
        'custom_placeholder_section'
    );

    add_settings_field(
        'custom_placeholder_border_focus_size',
        'Placeholder Border Focus Size (px)',
        'custom_placeholder_border_focus_size_render',
        'custom-placeholder-text',
        'custom_placeholder_section'
    );

    add_settings_field(
        'custom_placeholder_border_size',
        'Placeholder Border Size (px)',
        'custom_placeholder_border_size_render',
        'custom-placeholder-text',
        'custom_placeholder_section'
    );
}
add_action('admin_init', 'custom_placeholder_settings_init');

function custom_placeholder_section_callback() {
    echo 'Enter custom placeholder text and colours for the fields below:';
}

function custom_placeholder_email_render() {
    $value = get_option('custom_placeholder_email', 'Required');
    echo '<input type="text" name="custom_placeholder_email" value="' . esc_attr($value) . '">';
}

function custom_placeholder_name_render() {
    $value = get_option('custom_placeholder_name', 'Required');
    echo '<input type="text" name="custom_placeholder_name" value="' . esc_attr($value) . '">';
}

function custom_placeholder_border_colour_render() {
    $value = get_option('custom_placeholder_border_colour', '#000000');
    echo '<input type="color" name="custom_placeholder_border_colour" value="' . esc_attr($value) . '">';
}

function custom_placeholder_border_focus_colour_render() {
    $value = get_option('custom_placeholder_border_focus_colour', '#000000');
    echo '<input type="color" name="custom_placeholder_border_focus_colour" value="' . esc_attr($value) . '">';
}

function custom_placeholder_initial_text_colour_render() {
    $value = get_option('custom_placeholder_initial_text_colour', '#000000');
    echo '<input type="color" name="custom_placeholder_initial_text_colour" value="' . esc_attr($value) . '">';
}

function custom_placeholder_border_focus_size_render() {
    $value = get_option('custom_placeholder_border_focus_size', '1');
    echo '<input type="number" name="custom_placeholder_border_focus_size" value="' . esc_attr($value) . '" min="1">';
}

function custom_placeholder_border_size_render() {
    $value = get_option('custom_placeholder_border_size', '1');
    echo '<input type="number" name="custom_placeholder_border_size" value="' . esc_attr($value) . '" min="1">';
}

function custom_placeholder_settings_page() {
    ?>
    <form action="options.php" method="post">
        <h1>Custom Placeholder Text Settings</h1>
        <?php
        settings_fields('customPlaceholder');
        do_settings_sections('custom-placeholder-text');
        submit_button();
        ?>
    </form>
    <?php
}

// Enqueue jQuery
function custom_enqueue_scripts() {
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');

// Change placeholder text and colours
function custom_change_ticket_placeholder() {
    $email_placeholder = get_option('custom_placeholder_email', 'Required');
    $name_placeholder = get_option('custom_placeholder_name', 'Required');
    $placeholder_border_colour = get_option('custom_placeholder_border_colour', '#000000');
    $placeholder_border_focus_colour = get_option('custom_placeholder_border_focus_colour', '#000000');
    $placeholder_initial_text_colour = get_option('custom_placeholder_initial_text_colour', '#000000');
    $placeholder_border_focus_size = get_option('custom_placeholder_border_focus_size', '1') . 'px';
    $placeholder_border_size = get_option('custom_placeholder_border_size', '1') . 'px';
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            function changePlaceholders() {
                const emailFields = document.querySelectorAll('.tribe-tickets__form-field--email .tribe-tickets__form-field-input-wrapper input[type=email]');
                const nameFields = document.querySelectorAll('.tribe-tickets__form-field-input-wrapper input[type=text][name*="meta"][placeholder="Optional"]');

                emailFields.forEach(function(field) {
                    if (field.placeholder === 'Optional') {
                        field.placeholder = '<?php echo esc_js($email_placeholder); ?>';
                        field.style.setProperty('border-color', '<?php echo esc_js($placeholder_border_colour); ?>', 'important');
                        field.style.setProperty('--placeholder-color', '<?php echo esc_js($placeholder_initial_text_colour); ?>');
                        field.style.setProperty('border-width', '<?php echo esc_js($placeholder_border_size); ?>', 'important');
                        field.addEventListener('focus', function() {
                            field.style.setProperty('border-color', '<?php echo esc_js($placeholder_border_focus_colour); ?>', 'important');
                            field.style.setProperty('outline', 'none', 'important');
                            field.style.setProperty('border-width', '<?php echo esc_js($placeholder_border_focus_size); ?>', 'important');
                        });
                        field.addEventListener('blur', function() {
                            field.style.setProperty('border-color', '<?php echo esc_js($placeholder_border_colour); ?>', 'important');
                            field.style.setProperty('border-width', '<?php echo esc_js($placeholder_border_size); ?>', 'important');
                        });
                    }
                });

                nameFields.forEach(function(field) {
                    if (field.placeholder === 'Optional') {
                        field.placeholder = '<?php echo esc_js($name_placeholder); ?>';
                        field.style.setProperty('border-color', '<?php echo esc_js($placeholder_border_colour); ?>', 'important');
                        field.style.setProperty('--placeholder-color', '<?php echo esc_js($placeholder_initial_text_colour); ?>');
                        field.style.setProperty('border-width', '<?php echo esc_js($placeholder_border_size); ?>', 'important');
                        field.addEventListener('focus', function() {
                            field.style.setProperty('border-color', '<?php echo esc_js($placeholder_border_focus_colour); ?>', 'important');
                            field.style.setProperty('outline', 'none', 'important');
                            field.style.setProperty('border-width', '<?php echo esc_js($placeholder_border_focus_size); ?>', 'important');
                        });
                        field.addEventListener('blur', function() {
                            field.style.setProperty('border-color', '<?php echo esc_js($placeholder_border_colour); ?>', 'important');
                            field.style.setProperty('border-width', '<?php echo esc_js($placeholder_border_size); ?>', 'important');
                        });
                    }
                });
            }

            const observer = new MutationObserver(changePlaceholders);
            observer.observe(document.body, { childList: true, subtree: true });

            // Initial run in case fields are already in the DOM
            changePlaceholders();
        });

        // Add CSS for placeholder color
        const style = document.createElement('style');
        style.innerHTML = `
            .tribe-tickets__form-field--email .tribe-tickets__form-field-input-wrapper input[type=email]::-webkit-input-placeholder { color: var(--placeholder-color) !important; }
            .tribe-tickets__form-field--email .tribe-tickets__form-field-input-wrapper input[type=email]:-ms-input-placeholder { color: var(--placeholder-color) !important; }
            .tribe-tickets__form-field--email .tribe-tickets__form-field-input-wrapper input[type=email]::placeholder { color: var(--placeholder-color) !important; }
            .tribe-tickets__form-field--name .tribe-tickets__form-field-input-wrapper input[type=text]::-webkit-input-placeholder { color: var(--placeholder-color) !important; }
            .tribe-tickets__form-field--name .tribe-tickets__form-field-input-wrapper input[type=text]:-ms-input-placeholder { color: var(--placeholder-color) !important; }
            .tribe-tickets__form-field--name .tribe-tickets__form-field-input-wrapper input[type=text]::placeholder { color: var(--placeholder-color) !important; }
            .tribe-tickets__form-field--email .tribe-tickets__form-field-input-wrapper input[type=email]:focus { border-color: <?php echo esc_js($placeholder_border_focus_colour); ?> !important; outline: none !important; border-width: <?php echo esc_js($placeholder_border_focus_size); ?> !important; }
            .tribe-tickets__form-field--email .tribe-tickets__form-field-input-wrapper input[type=email] { border-width: <?php echo esc_js($placeholder_border_size); ?> !important; }
            .tribe-tickets__form-field--name .tribe-tickets__form-field-input-wrapper input[type=text]:focus { border-color: <?php echo esc_js($placeholder_border_focus_colour); ?> !important; outline: none !important; border-width: <?php echo esc_js($placeholder_border_focus_size); ?> !important; }
            .tribe-tickets__form-field--name .tribe-tickets__form-field-input-wrapper input[type=text] { border-width: <?php echo esc_js($placeholder_border_size); ?> !important; }
            .tribe-tickets__form-field-input-wrapper input[type=text]::placeholder { color: var(--placeholder-color) !important; }
        `;
        document.head.appendChild(style);
    </script>
    <?php
}
add_action('wp_footer', 'custom_change_ticket_placeholder');
?>
