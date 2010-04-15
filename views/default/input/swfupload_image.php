
<?php 
    // TODO : only works 1 per page due to internalid, swfupload.js include
    
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
    
    echo elgg_view('input/hidden', array(
        'internalname' => $vars['internalname'], 
        'internalid' => 'imageUpload', 
        'value' => ''
    )); 
?>

<span id='imageUploadContainer'></span>
<div id='imageUploadProgress'></div>
<script type='text/javascript' src='_media/swfupload.js'></script>
<script type="text/javascript">

    image_uploader({     
        thumbnail_size: <?php echo json_encode(@$vars['thumbnail_size'] ?: 'small') ?>,
        max_width: <?php echo $maxWidth ?>,
        max_height: <?php echo $maxHeight ?>,
        progress_id: 'imageUploadProgress',
        placeholder_id: 'imageUploadContainer',        
        button_message: <?php echo json_encode(elgg_echo('upload:browse')) ?>,
        queue_error_message: <?php echo json_encode(elgg_echo('upload:image:error')) ?>,
        processing_message: <?php echo json_encode(elgg_echo('upload:image:processing')) ?>,
        upload_progress_message: <?php echo json_encode(elgg_echo('upload:image:uploading')) ?>,
        loading_preview_message: <?php echo json_encode(elgg_echo('upload:image:complete')) ?>,
        upload_error_message: <?php echo json_encode(elgg_echo('upload:image:error')) ?>,
        result_id: 'imageUpload',        
        sizes: <?php echo json_encode(json_encode($sizes)) ?>
    });
</script>    
    