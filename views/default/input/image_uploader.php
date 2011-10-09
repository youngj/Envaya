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

    if (!isset($vars['uploader_class']))
    {    
        $vars['uploader_class'] = "SingleImageUploader";
    }
    $vars['uploader_args'] = array(
        'file_types' => implode(",", UploadedFile::$image_extensions),
        'file_types_description' => "Images",
        'thumbnail_size' => @$vars['thumbnail_size'] ?: 'small',
        'max_width' => $maxWidth,
        'max_height' => $maxHeight,        
        'multi_selection' => false,
        'format_error_message' => __('upload:invalid_image_format'),
        'upload_progress_message' =>  __('upload:image:uploading'),
        'upload_error_message' => __('upload:image:error'),
        'queue_error_message' => __('upload:image:error'),
        'post_params' => array(
            'mode' => 'image',
            'sizes' => json_encode($sizes)
        )
    );
    
    echo view('input/uploader', $vars);
?>    