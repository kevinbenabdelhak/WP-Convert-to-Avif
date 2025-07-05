<?php

if (!defined('ABSPATH')) {
    exit; 
}


function wpc2a_handle_301_redirects() {
    if (is_admin()) {
        return; 
    }

    $options = get_option('wpc2a_options');
    if (isset($options['redirects_list']) && !empty($options['redirects_list'])) {
        $redirects = array_filter(array_map('trim', explode(PHP_EOL, $options['redirects_list'])));
        foreach ($redirects as $redirect) {
            list($old_url, $new_url) = array_map('trim', explode(' ', $redirect, 2));
            if ($old_url && $new_url && strpos($_SERVER['REQUEST_URI'], parse_url($old_url, PHP_URL_PATH)) !== false) {
                wp_redirect($new_url, 301);
                exit;
            }
        }
    }
}

function wpc2a_add_301_redirect($old_url, $new_url) {
    $options = get_option('wpc2a_options');
    $redirects = isset($options['redirects_list']) ? $options['redirects_list'] : '';
    $redirects .= $old_url . ' ' . $new_url . PHP_EOL;
    $options['redirects_list'] = $redirects;
    update_option('wpc2a_options', $options);
}