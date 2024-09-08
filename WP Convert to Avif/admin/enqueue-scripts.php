<?php

if (!defined('ABSPATH')) {
    exit; 
}


function wpc2a_enqueue_scripts($hook) {
    if ($hook !== 'upload.php') {
        return;
    }

    wp_enqueue_script('jquery');

    wp_add_inline_script('jquery', '
    jQuery(document).ready(function($) {
        if ($("select[name=\'action\'] option[value=\'convert_to_avif\']").length === 0) {
            $("select[name=\'action\'], select[name=\'action2\']").append(\'<option value="convert_to_avif">' . __('Convertir en AVIF', 'wpc2a') . '</option>\');
        }

        $(document).on("click", "#doaction, #doaction2", function(e) {
            var action = $("select[name=\'action\']").val() !== "-1" ? $("select[name=\'action\']").val() : $("select[name=\'action2\']").val();

            if (action !== "convert_to_avif") return;

            e.preventDefault();

            var attachment_ids = [];
            $("tbody th.check-column input[type=\'checkbox\']:checked").each(function() {
                attachment_ids.push($(this).val());
            });

            if (attachment_ids.length === 0) {
                alert("' . __('Aucune image sélectionnée', 'wpc2a') . '");
                return;
            }

            $("#bulk-action-loader").remove();
            $("#doaction, #doaction2").after("<div id=\'bulk-action-loader\'><span class=\'spinner is-active\' style=\'margin-left: 10px;\'></span> <span id=\'conversion-progress\'>0 / " + attachment_ids.length + " convertis</span></div>");

            var convertedCount = 0;
            var failedCount = 0;

            function convertNext(index) {
                if (index >= attachment_ids.length) {
                    $("#bulk-action-loader").remove();
                    var message = convertedCount + " image(s) converties avec succès.";
                    if (failedCount > 0) {
                        message += " " + failedCount + " échec(s).";
                    }
                    $("<div class=\'notice notice-success is-dismissible\'><p>" + message + "</p></div>").insertAfter(".wp-header-end");
                    location.reload();
                    return;
                }

                $.ajax({
                    url: wpc2a_ajax.ajax_url,
                    method: "POST",
                    data: {
                        action: "wpc2a_bulk_convert",
                        nonce: wpc2a_ajax.nonce,
                        attachment_id: attachment_ids[index]
                    },
                    success: function(response) {
                        if (response.success) {
                            convertedCount++;
                        } else {
                            failedCount++;
                            console.error("Erreur de conversion pour l\'image ID " + attachment_ids[index] + ": " + response.data);
                        }
                        $("#conversion-progress").text(convertedCount + " / " + attachment_ids.length + " convertis");
                        convertNext(index + 1);
                    },
                    error: function() {
                        failedCount++;
                        console.error("Erreur de conversion pour l\'image ID " + attachment_ids[index]);
                        $("#conversion-progress").text(convertedCount + " / " + attachment_ids.length + " convertis");
                        convertNext(index + 1);
                    }
                });
            }

            convertNext(0);
        });
    });');

    wp_localize_script('jquery', 'wpc2a_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wpc2a_nonce')
    ]);
}