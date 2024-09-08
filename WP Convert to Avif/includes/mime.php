<?php

if (!defined('ABSPATH')) {
    exit; 
}


function wpc2a_add_avif_mime_type($mimes) {
    $mimes['avif'] = 'image/avif';
    return $mimes;
}