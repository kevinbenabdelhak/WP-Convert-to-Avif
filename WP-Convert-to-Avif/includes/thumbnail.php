<?php

if (!defined('ABSPATH')) {
    exit; 
}


function wpc2a_set_avif_thumbnail($metadata, $attachment_id) {
    $mime_type = get_post_mime_type($attachment_id);
    if ($mime_type === 'image/avif') {
        $attachment_path = get_attached_file($attachment_id);
        if ($attachment_path) {
    
            if (function_exists('wp_generate_attachment_metadata')) {
                wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $attachment_path));
            }
        }
    }
    return $metadata;
}