<?php

if (!defined('ABSPATH')) {
    exit; 
}


function wpc2a_register_bulk_actions($bulk_actions) {
    $bulk_actions['convert_to_avif'] = __('Convertir en AVIF', 'wpc2a');
    return $bulk_actions;
}

function wpc2a_handle_bulk_action($redirect_to, $doaction, $post_ids) {
    if ($doaction !== 'convert_to_avif') {
        return $redirect_to;
    }

    $redirect_to = add_query_arg('bulk_convert_to_avif', count($post_ids), $redirect_to);
    return $redirect_to;
}