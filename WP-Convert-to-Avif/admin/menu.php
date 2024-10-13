<?php

if (!defined('ABSPATH')) {
    exit; 
}


function wpc2a_add_admin_menu() {
    add_management_page(
        __('WP Convert to Avif', 'wpc2a'),
        __('WP Convert to Avif', 'wpc2a'),
        'manage_options',
        'wpc2a',
        'wpc2a_options_page'
    );
}