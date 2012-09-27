<?php
    $name = null;
    $value = null;
    $track_dirty = false;
    $id = "upload$INCLUDE_COUNT";
    $progress_id = "uploadProgress$INCLUDE_COUNT";
    $uploader_class = 'FileUploader';
    $uploader_args = null;    
    $jsname = null;
    extract($vars);

    echo view('js/uploader');

    $prevInput = Input::restore_value($name, $value, $track_dirty);

    $uploader_args['progress_id'] = $progress_id;
    $uploader_args['track_dirty'] = $track_dirty;       
    $uploader_args['container_id'] = "uploadContainer$INCLUDE_COUNT";
    $uploader_args['browse_id'] = "uploadBrowse$INCLUDE_COUNT";
    $uploader_args['result_id'] = $id;
    
    echo view('input/hidden', array(
        'name' => $name,
        'id' => $id,
        'value' => $prevInput
    ));
?>
<div id='<?php echo $uploader_args['container_id']; ?>'>
<a href='javascript:void(0)' style='font-weight:bold' id='<?php echo $uploader_args['browse_id']; ?>'><?php echo __('upload:browse'); ?></a>
</div>
<div id='<?php echo $progress_id ?>' class='imageUploadProgress'></div>
<script type="text/javascript">
var uploader = new <?php echo $uploader_class; ?>(<?php 
    echo view('input/uploader_args', 
        array('args' => $uploader_args)
    ); ?>);

<?php if ($jsname) { ?>
window[<?php echo json_encode($jsname) ?>] = uploader;
<?php } ?>
</script>
