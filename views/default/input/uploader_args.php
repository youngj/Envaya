<?php 
    $post_params = array(
        'session_id' => session_id(),
        'lang' => Language::get_current_code(),    
    );

    $args = array(
        'runtimes' => Config::get('plupload_runtimes'),
        'server_processing_message' => __('upload:server_processing'),
        'size_error_message' => __('upload:size_error'),
        'upload_progress_message' =>  __('upload:uploading'),
        'upload_error_message' => __('upload:error'),
        'queue_error_message' => __('upload:error'),
        'processing_message' => __('upload:image:processing'),
        'loading_preview_message' => __('upload:complete'),
        'initial_message' => '',
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