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

    $vars['swfupload_class'] = "SingleImageUploader";
    $vars['swfupload_args'] = array(
        'thumbnail_size' => @$vars['thumbnail_size'] ?: 'small',
        'max_width' => $maxWidth,
        'max_height' => $maxHeight,
        'sizes' => json_encode($sizes)
    );
    
    echo view('input/swfupload', $vars);
?>    