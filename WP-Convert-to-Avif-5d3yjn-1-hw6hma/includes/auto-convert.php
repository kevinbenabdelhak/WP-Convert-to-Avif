<?php

if (!defined('ABSPATH')) {
    exit; 
}

function wpc2a_custom_cron_schedules($schedules) {
    if (!isset($schedules['every_minute'])) {
        $schedules['every_minute'] = array(
            'interval' => 60, 
            'display'  => __('Every Minute', 'wpc2a')
        );
    }
    return $schedules;
}
add_filter('cron_schedules', 'wpc2a_custom_cron_schedules');

function wpc2a_auto_convert_to_avif($attachment_id) {
    $options = get_option('wpc2a_options');

    if (isset($options['auto_convert_enable']) && $options['auto_convert_enable'] === 'yes') {
        $path = get_attached_file($attachment_id);
        $mime_type = mime_content_type($path);

   
        if (in_array($mime_type, ['image/jpeg', 'image/webp'])) {
            if (isset($options['conversion_timing']) && $options['conversion_timing'] === 'delayed') {
                
                
                if (!wp_next_scheduled('wpc2a_delayed_conversion', array('attachment_id' => $attachment_id))) {
                    wp_schedule_single_event(time() + 60, 'wpc2a_delayed_conversion', array('attachment_id' => $attachment_id));
                }
            } else {
                
              
                wpc2a_convert_to_avif($path, $options, $attachment_id, true);
            }
        }
    }
}

add_action('wpc2a_delayed_conversion', 'wpc2a_handle_delayed_conversion');

function wpc2a_handle_delayed_conversion($attachment_id) {
    $options = get_option('wpc2a_options');
    $path = get_attached_file($attachment_id);
    $mime_type = mime_content_type($path);

    if (in_array($mime_type, ['image/jpeg', 'image/webp'])) {
        wpc2a_convert_to_avif($path, $options, $attachment_id, true);
    }
}