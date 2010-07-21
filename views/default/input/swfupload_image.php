
<?php
    global $SWFUPLOAD_INCLUDE_COUNT;
    if (!isset($SWFUPLOAD_INCLUDE_COUNT))
    {
        $SWFUPLOAD_INCLUDE_COUNT = 0;
        ?>
            <script type='text/javascript'>
                setTimeout(function() {
                    var script = document.createElement('script');
                    script.type = 'text/javascript';
                    script.src = '_media/swfupload.js?v6';
                    document.getElementsByTagName("head").item(0).appendChild(script);
                }, 1);
            </script>
        <?php
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

    $resultId = @$vars['internalid'] ?: "imageUpload$SWFUPLOAD_INCLUDE_COUNT";
    $progressId = @$vars['progressid'] ?: "imageUploadProgress$SWFUPLOAD_INCLUDE_COUNT";

    echo elgg_view('input/hidden', array(
        'internalname' => $vars['internalname'],
        'internalid' => $resultId,
        'value' => $prevInput
    ));
?>

<span id='imageUploadContainer<?php echo $SWFUPLOAD_INCLUDE_COUNT ?>'></span>
<div id='<?php echo $progressId ?>' class='imageUploadProgress'></div>
<script type="text/javascript">
(function() {
    function tryInitSWFUpload()
    {
        if (!window.SingleImageUploader)
        {
            return;
        }

        clearInterval(checkSWFUploadInterval);

        new SingleImageUploader(<?php echo elgg_view('input/swfupload_args', array(
            'args' => array(
                'trackDirty' => (@$vars['trackDirty'] ? true : false),
                'thumbnail_size' => @$vars['thumbnail_size'] ?: 'small',
                'max_width' => $maxWidth,
                'max_height' => $maxHeight,
                'progress_id' => $progressId,
                'placeholder_id' => "imageUploadContainer$SWFUPLOAD_INCLUDE_COUNT",
                'result_id' => $resultId,
                'sizes' => json_encode($sizes)
            )
        )) ?>);
    }

    var checkSWFUploadInterval = setInterval(tryInitSWFUpload, 250);

})();
</script>
