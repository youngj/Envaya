<?php
    global $SWFUPLOAD_INCLUDE_COUNT;
    if (!isset($SWFUPLOAD_INCLUDE_COUNT))
    {
        $SWFUPLOAD_INCLUDE_COUNT = 0;
        echo "<script type='text/javascript' src='/_media/swfupload.js?v{$vars['config']->cache_version}'></script>";
    }
    else
    {
        $SWFUPLOAD_INCLUDE_COUNT++;
    }

    $prevInput = restore_input($vars['internalname'], @$vars['value']);

    $resultId = @$vars['internalid'] ?: "imageUpload$SWFUPLOAD_INCLUDE_COUNT";
    $progressId = @$vars['progressid'] ?: "imageUploadProgress$SWFUPLOAD_INCLUDE_COUNT";

    $swfupload_args = $vars['swfupload_args'];
    $swfupload_args['progress_id'] = $progressId;
    $swfupload_args['trackDirty'] = (@$vars['trackDirty'] ? true : false);       
    $swfupload_args['placeholder_id'] = "imageUploadContainer$SWFUPLOAD_INCLUDE_COUNT";
    $swfupload_args['result_id'] = $resultId;
    
    echo view('input/hidden', array(
        'internalname' => $vars['internalname'],
        'internalid' => $resultId,
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
