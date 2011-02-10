<?php 
    $post_params = array(
        'session_id' => session_id(),
        'lang' => get_language(),    
    );

    $args = array(
        'button_message' => __('upload:browse'),
        'upload_progress_message' =>  __('upload:uploading'),
        'upload_error_message' => __('upload:error'),
        'queue_error_message' => __('upload:error'),
        'processing_message' => __('upload:image:processing'),
        'loading_preview_message' => __('upload:complete'),
        'recommend_flash_message' => '',
    );

    foreach ($vars['args'] as $k => $v)
    {
        if ($k == 'post_params')
        {
            foreach ($v as $post_k => $post_v)
            {
                $post_params[$post_k] = $post_v;
            }
        }
        else
        {
            $args[$k] = $v;
        }
    }
    
    $args['post_params'] = $post_params;
    
    echo json_encode($args);