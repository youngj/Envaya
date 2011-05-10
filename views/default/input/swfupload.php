<?php

    $SWFUPLOAD_INCLUDE_COUNT = $vars['include_count'];

    echo view('js/swfupload');

    $prevInput = restore_input($vars['name'], @$vars['value'], @$vars['trackDirty']);

    $resultId = @$vars['id'] ?: "imageUpload$SWFUPLOAD_INCLUDE_COUNT";
    $progressId = @$vars['progressid'] ?: "imageUploadProgress$SWFUPLOAD_INCLUDE_COUNT";

    $swfupload_args = $vars['swfupload_args'];
    $swfupload_args['progress_id'] = $progressId;
    $swfupload_args['trackDirty'] = (@$vars['trackDirty'] ? true : false);       
    $swfupload_args['placeholder_id'] = "imageUploadContainer$SWFUPLOAD_INCLUDE_COUNT";
    $swfupload_args['result_id'] = $resultId;
    
    echo view('input/hidden', array(
        'name' => $vars['name'],
        'id' => $resultId,
        'value' => $prevInput
    ));
?>
<span id='imageUploadContainer<?php echo $SWFUPLOAD_INCLUDE_COUNT ?>'></span>
<div id='<?php echo $progressId ?>' class='imageUploadProgress'></div>
<script type="text/javascript">
var uploader = new <?php echo $vars['swfupload_class']; ?>(<?php 
    echo view('input/swfupload_args', 
        array('args' => $swfupload_args)
    ); ?>);

<?php if (@$vars['jsname']) { ?>
window[<?php echo json_encode($vars['jsname']) ?>] = uploader;
<?php } ?>
</script>
