<?php

if (!defined('ABSPATH')) {
    exit; 
}


function wpc2a_add_custom_admin_styles() {
    echo '<style>
        table.media .column-title .media-icon img {
            width: 100%;
        }
    </style>';
}