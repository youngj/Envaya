<?php

    $sizes = $vars['sizes'];

    $maxWidth = -1;
    $maxHeight = -1;

    foreach ($sizes as $name => $wxh)
    {
        $size = explode("x", $wxh);
        if ($size[0] > $maxWidth)
        {
            $maxWidth = $size[0];
            $maxHeight = $size[1];
        }
    }

    if (!isset($vars['swfupload_class']))
    {    
        $vars['swfupload_class'] = "SingleImageUploader";
    }
    $vars['swfupload_args'] = array(
        'thumbnail_size' => @$vars['thumbnail_size'] ?: 'small',
        'max_width' => $maxWidth,
        'max_height' => $maxHeight,
        'upload_progress_message' =>  __('upload:image:uploading'),
        'upload_error_message' => __('upload:image:error'),
        'queue_error_message' => __('upload:image:error'),
        'no_flash_message' => view('upload/recommend_flash_message'),        
        'post_params' => array(
            'sizes' => json_encode($sizes)
        )
    );
    
    echo view('input/swfupload', $vars);
?>    