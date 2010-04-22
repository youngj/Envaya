
<?php 
    global $SWFUPLOAD_INCLUDE_COUNT;
    if (!isset($SWFUPLOAD_INCLUDE_COUNT))
    {
        $SWFUPLOAD_INCLUDE_COUNT = 0;
        echo "<script type='text/javascript' src='_media/swfupload.js'></script>";
    }    
    else
    {
        $SWFUPLOAD_INCLUDE_COUNT++;
    }
        
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
    
    $prevInput = restore_input($vars['internalname'], '');
    
    echo elgg_view('input/hidden', array(
        'internalname' => $vars['internalname'], 
        'internalid' => "imageUpload$SWFUPLOAD_INCLUDE_COUNT", 
        'value' => $prevInput
    )); 
?>

<span id='imageUploadContainer<?php echo $SWFUPLOAD_INCLUDE_COUNT ?>'></span>
<div id='imageUploadProgress<?php echo $SWFUPLOAD_INCLUDE_COUNT ?>' class='imageUploadProgress'></div>
<script type="text/javascript">
    image_uploader({     
        session_id: <?php echo json_encode(session_id()); ?>,
        trackDirty: <?php echo (@$vars['trackDirty'] ? 'true' : 'false'); ?>,
        thumbnail_size: <?php echo json_encode(@$vars['thumbnail_size'] ?: 'small') ?>,
        max_width: <?php echo $maxWidth ?>,
        max_height: <?php echo $maxHeight ?>,
        progress_id: 'imageUploadProgress<?php echo $SWFUPLOAD_INCLUDE_COUNT ?>',
        placeholder_id: 'imageUploadContainer<?php echo $SWFUPLOAD_INCLUDE_COUNT ?>',        
        result_id: 'imageUpload<?php echo $SWFUPLOAD_INCLUDE_COUNT ?>',        
        button_message: <?php echo json_encode(elgg_echo('upload:browse')) ?>,
        queue_error_message: <?php echo json_encode(elgg_echo('upload:image:error')) ?>,
        processing_message: <?php echo json_encode(elgg_echo('upload:image:processing')) ?>,
        upload_progress_message: <?php echo json_encode(elgg_echo('upload:image:uploading')) ?>,
        loading_preview_message: <?php echo json_encode(elgg_echo('upload:image:complete')) ?>,
        upload_error_message: <?php echo json_encode(elgg_echo('upload:image:error')) ?>,        
        sizes: <?php echo json_encode(json_encode($sizes)) ?>
    });
</script>    
    