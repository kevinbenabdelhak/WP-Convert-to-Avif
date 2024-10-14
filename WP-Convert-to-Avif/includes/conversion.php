<?php
if (!defined('ABSPATH')) {
    exit;
}

function wpc2a_convert_to_avif($path, $options, $attachment_id, $is_auto = false) {
    $image = @imagecreatefromjpeg($path);
    if (!$image) {
        $image = @imagecreatefrompng($path);
    }
    if (!$image) {
        $image = @imagecreatefromwebp($path);
    }

    if (!$image) {
        return false;
    }

    $upload_dir = wp_upload_dir();
    $new_filename = wp_unique_filename($upload_dir['path'], pathinfo($path, PATHINFO_FILENAME) . '.avif');
    $avif_path = $upload_dir['path'] . '/' . $new_filename;

    ob_start();
    imageavif($image);
    $avif_data = ob_get_clean();
    file_put_contents($avif_path, $avif_data);

    imagedestroy($image);

    $original_filename = pathinfo($path, PATHINFO_FILENAME) . '.avif';
    $original_dir = pathinfo($path, PATHINFO_DIRNAME);
    $final_avif_path = $original_dir . '/' . $original_filename;

    if (!rename($avif_path, $final_avif_path)) {
        return false;
    }

    $old_url = wp_get_attachment_url($attachment_id);
    $new_url = str_replace(wp_basename($old_url), $original_filename, $old_url);

    global $wpdb;
    $wpdb->query(
        $wpdb->prepare(
            "UPDATE {$wpdb->posts} SET guid = %s, post_mime_type = 'image/avif' WHERE ID = %d",
            $new_url,
            $attachment_id
        )
    );

    update_attached_file($attachment_id, $final_avif_path);

    $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)",
        $old_url, $new_url
    ));
    $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s)",
        $old_url,
        $new_url
    ));
	
	  // mettre à jour la date de la publication à laquelle l'image est attachée
    $attachment_post = get_post($attachment_id);
    $post_id = $attachment_post->post_parent;

    if ($post_id) {
        $current_time = current_time('mysql');
        $post_data = array(
            'ID'                => $post_id,
            'post_modified'     => $current_time,
            'post_modified_gmt' => get_gmt_from_date($current_time),
        );

        wp_update_post($post_data);
	
    if (!$is_auto && isset($options['auto_redirect']) && $options['auto_redirect']) {
        wpc2a_add_301_redirect($old_url, $new_url);
    }

    if (file_exists($path)) {
        unlink($path);
    }

    $attach_data = wp_generate_attachment_metadata($attachment_id, $final_avif_path);
    wp_update_attachment_metadata($attachment_id, $attach_data);

  
    }

    return true;
}