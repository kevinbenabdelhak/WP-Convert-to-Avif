<?php

if (!defined('ABSPATH')) {
    exit; 
}

function wpc2a_display_conversion_notices() {
    if (isset($_GET['bulk_convert_to_avif'])) {
        $count = intval($_GET['bulk_convert_to_avif']);
        echo '<div class="notice notice-success is-dismissible"><p>' .
             sprintf(_n('%s image a été convertie en AVIF avec succès.', '%s images ont été converties en AVIF avec succès.', $count, 'wpc2a'), number_format_i18n($count)) .
             '</p></div>';
    }
}