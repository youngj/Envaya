<?php
    $name = null;
    $value = null;
    $track_dirty = false;
    $id = "imageUpload$INCLUDE_COUNT";
    $progress_id = "imageUploadProgress$INCLUDE_COUNT";
    $swfupload_class = 'FileUploader';
    $swfupload_args = null;    
    $jsname = null;
    extract($vars);

    echo view('js/swfupload');

    $prevInput = restore_input($name, $value, $track_dirty);

    $swfupload_args['progress_id'] = $progress_id;
    $swfupload_args['track_dirty'] = $track_dirty;       
    $swfupload_args['placeholder_id'] = "imageUploadContainer$INCLUDE_COUNT";
    $swfupload_args['result_id'] = $id;
    
    echo view('input/hidden', array(
        'name' => $name,
        'id' => $id,
        'value' => $prevInput
    ));
?>
<span id='imageUploadContainer<?php echo $INCLUDE_COUNT ?>'></span>
<div id='<?php echo $progress_id ?>' class='imageUploadProgress'></div>
<script type="text/javascript">
var uploader = new <?php echo $swfupload_class; ?>(<?php 
    echo view('input/swfupload_args', 
        array('args' => $swfupload_args)
    ); ?>);

<?php if ($jsname) { ?>
window[<?php echo json_encode($jsname) ?>] = uploader;
<?php } ?>
</script>
