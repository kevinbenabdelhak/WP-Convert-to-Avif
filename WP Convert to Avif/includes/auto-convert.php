<?php

if (!defined('ABSPATH')) {
    exit; 
}


function wpc2a_auto_convert_to_avif($attachment_id) {
    $options = get_option('wpc2a_options');
    if (isset($options['auto_convert']) && $options['auto_convert']) {
        $path = get_attached_file($attachment_id);
        $mime_type = mime_content_type($path);

        if (in_array($mime_type, ['image/jpeg', 'image/png', 'image/webp'])) {
            wpc2a_convert_to_avif($path, $options, $attachment_id, true);
        }
    }
}