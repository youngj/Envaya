<?php 
    $args = array(
        'session_id' => session_id(),
        'button_message' => __('upload:browse'),
        'queue_error_message' => __('upload:image:error'),
        'processing_message' => __('upload:image:processing'),
        'upload_progress_message' =>  __('upload:image:uploading'),
        'loading_preview_message' => __('upload:image:complete'),
        'upload_error_message' => __('upload:image:error'),
        'recommend_flash_message' => "<div class='help' style='font-size:10px'>".
            sprintf(__('upload:image:recommend_flash'),
                    "<a href='http://www.adobe.com/go/getflash' target='_blank'>Adobe Flash 10</a>")
            ."</div>"
    );

    foreach ($vars['args'] as $k => $v)
    {
        $args[$k] = $v;
    }
    echo json_encode($args);