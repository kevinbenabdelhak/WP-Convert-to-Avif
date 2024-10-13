<?php
/*
* Plugin Name: WP Convert to Avif
* Plugin URI: https://kevin-benabdelhak.fr/plugins/wp-convert-to-avif/
* Description: WP Convert to Avif est un plugin WordPress qui permet de convertir toutes vos images en fichier avif facilement grâce aux actions groupées dans les médias. Accédez à une page d'option pratique pour activer la conversion auto.
* Version: 1.1
* Author: Kevin BENABDELHAK
* Author URI: https://kevin-benabdelhak.fr
* Contributors: kevinbenabdelhak
*/

if (!defined('ABSPATH')) {
    exit; 
}

define('WPC2A_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPC2A_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Charger les fichiers nécessaires
require_once(WPC2A_PLUGIN_PATH . 'admin/menu.php');
require_once(WPC2A_PLUGIN_PATH . 'admin/settings.php');
require_once(WPC2A_PLUGIN_PATH . 'admin/enqueue-scripts.php');
require_once(WPC2A_PLUGIN_PATH . 'admin/bulk-actions.php');
require_once(WPC2A_PLUGIN_PATH . 'admin/ajax.php');
require_once(WPC2A_PLUGIN_PATH . 'admin/admin-styles.php');
require_once(WPC2A_PLUGIN_PATH . 'admin/notices.php');

require_once(WPC2A_PLUGIN_PATH . 'includes/conversion.php');
require_once(WPC2A_PLUGIN_PATH . 'includes/auto-convert.php');
require_once(WPC2A_PLUGIN_PATH . 'includes/redirects.php');
require_once(WPC2A_PLUGIN_PATH . 'includes/mime.php');
require_once(WPC2A_PLUGIN_PATH . 'includes/thumbnail.php');

// Initialiser le plugin
add_action('admin_menu', 'wpc2a_add_admin_menu');
add_action('admin_init', 'wpc2a_settings_init');
add_action('admin_enqueue_scripts', 'wpc2a_enqueue_scripts');
add_action('wp_ajax_wpc2a_bulk_convert', 'wpc2a_handle_ajax_convert_to_avif');
add_action('add_attachment', 'wpc2a_auto_convert_to_avif');

add_filter('upload_mimes', 'wpc2a_add_avif_mime_type');
add_filter('bulk_actions-upload', 'wpc2a_register_bulk_actions');
add_filter('handle_bulk_actions-upload', 'wpc2a_handle_bulk_action', 10, 3);
add_action('wp_generate_attachment_metadata', 'wpc2a_set_avif_thumbnail', 10, 2);

add_action('admin_head', 'wpc2a_add_custom_admin_styles');
add_action('template_redirect', 'wpc2a_handle_301_redirects');

add_action('admin_notices', 'wpc2a_display_conversion_notices');






/* desactiver evenemnts */


register_deactivation_hook(__FILE__, 'wpc2a_clear_scheduled_events');
function wpc2a_clear_scheduled_events() {
    $crons = _get_cron_array();
    if ($crons) {
        foreach ($crons as $timestamp => $cron) {
            foreach ($cron as $hook => $dings) {
                if ($hook == 'wpc2a_delayed_conversion') {
                    unset($crons[$timestamp][$hook]);
                }
            }
        }

        _set_cron_array($crons);
    }
}