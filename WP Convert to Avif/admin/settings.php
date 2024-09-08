<?php

if (!defined('ABSPATH')) {
    exit; 
}


function wpc2a_settings_init() {
    register_setting('wpc2a_settings', 'wpc2a_options');

    add_settings_section(
        'wpc2a_settings_section',
        __('Options', 'wpc2a'),
        null,
        'wpc2a_settings'
    );

    add_settings_field(
        'wpc2a_auto_convert',
        __('Conversion automatique', 'wpc2a'),
        'wpc2a_auto_convert_render',
        'wpc2a_settings',
        'wpc2a_settings_section'
    );

    add_settings_field(
        'wpc2a_auto_redirect',
        __('301 Automatique', 'wpc2a'),
        'wpc2a_auto_redirect_render',
        'wpc2a_settings',
        'wpc2a_settings_section'
    );

    add_settings_field(
        'wpc2a_redirects_list',
        __('Liste de redirections 301', 'wpc2a'),
        'wpc2a_redirects_list_render',
        'wpc2a_settings',
        'wpc2a_settings_section'
    );
}

function wpc2a_auto_convert_render() {
    $options = get_option('wpc2a_options');
    ?>
    <input type='checkbox' name='wpc2a_options[auto_convert]' <?php checked($options['auto_convert'], 1); ?> value='1'>
    <label for='wpc2a_options[auto_convert]'><?php _e('Convertir automatiquement les images ajoutées', 'wpc2a'); ?></label>
    <?php
}

function wpc2a_auto_redirect_render() {
    $options = get_option('wpc2a_options');
    ?>
    <input type='checkbox' name='wpc2a_options[auto_redirect]' <?php checked($options['auto_redirect'], 1); ?> value='1' id="wpc2a_auto_redirect">
    <label for='wpc2a_options[auto_redirect]'><?php _e('Ajouter automatiquement des redirections 301', 'wpc2a'); ?></label>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const checkbox = document.getElementById("wpc2a_auto_redirect");
            const textarea = document.getElementById("wpc2a_redirects_list");

            manageTextareaVisibility();
            checkbox.addEventListener("change", manageTextareaVisibility);

            function manageTextareaVisibility() {
                textarea.style.display = checkbox.checked ? "block" : "none";
            }
        });
    </script>
    <?php
}

function wpc2a_redirects_list_render() {
    $options = get_option('wpc2a_options');
    ?>
    <textarea name='wpc2a_options[redirects_list]' id='wpc2a_redirects_list' rows='10' cols='50' style='width:100%; display:<?php echo isset($options['auto_redirect']) && $options['auto_redirect'] ? "block" : "none"; ?>;'><?php echo isset($options['redirects_list']) ? esc_textarea($options['redirects_list']) : ''; ?></textarea>
    <p><?php _e('Indiquez les redirections 301 ici. Une redirection par ligne avec le format: ancienne_url nouvelle_url', 'wpc2a'); ?></p>
    <?php
}

function wpc2a_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h2><?php _e('Paramètres de WP Convert to Avif', 'wpc2a'); ?></h2>
        <p><?php _e('Les images seront converties au format AVIF, remplaçant les images originales.', 'wpc2a'); ?></p>
        <?php
        settings_fields('wpc2a_settings');
        do_settings_sections('wpc2a_settings');
        submit_button();
        ?>
    </form>
    <?php
}