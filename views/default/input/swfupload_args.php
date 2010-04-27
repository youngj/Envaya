<?php 
    $args = array(
        'session_id' => session_id(),
        'button_message' => elgg_echo('upload:browse'),
        'queue_error_message' => elgg_echo('upload:image:error'),
        'processing_message' => elgg_echo('upload:image:processing'),
        'upload_progress_message' =>  elgg_echo('upload:image:uploading'),
        'loading_preview_message' => elgg_echo('upload:image:complete'),
        'upload_error_message' => elgg_echo('upload:image:error'),
        'recommend_flash_message' => "<div class='help' style='font-size:10px'>".
            sprintf(elgg_echo('upload:image:recommend_flash'),
                    "<a href='http://www.adobe.com/go/getflash' target='_blank'>Adobe Flash 10</a>")
            ."</div>"
    );

    foreach ($vars['args'] as $k => $v)
    {
        $args[$k] = $v;
    }
    echo json_encode($args);