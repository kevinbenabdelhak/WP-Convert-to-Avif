<?php

if (!defined('ABSPATH')) {
    exit; 
}


function wpc2a_handle_ajax_convert_to_avif() {
    check_ajax_referer('wpc2a_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Vous n\'avez pas les permissions suffisantes pour accéder à cette page.');
    }

    if (!isset($_POST['attachment_id'])) {
        wp_send_json_error('ID de pièce jointe manquant.');
    }

    $attachment_id = intval($_POST['attachment_id']);
    $path = get_attached_file($attachment_id);
    $mime_type = mime_content_type($path);

    if (!in_array($mime_type, ['image/jpeg', 'image/png', 'image/webp'])) {
        wp_send_json_error('Type de fichier non pris en charge.');
    }

    $options = get_option('wpc2a_options');
    $result = wpc2a_convert_to_avif($path, $options, $attachment_id, false);

    if ($result) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Échec de la conversion.');
    }
}