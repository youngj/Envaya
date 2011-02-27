<?php
    $UPLOAD_INCLUDE_COUNT = $vars['include_count'];
    
    $resultId = @$vars['id'] ?: "upload_result$UPLOAD_INCLUDE_COUNT";
    $progressId = "upload_progress$UPLOAD_INCLUDE_COUNT";
?>

<span id='upload_span<?php echo $UPLOAD_INCLUDE_COUNT; ?>'><?php
    $value = json_decode(@$vars['value'], true);
    $has_value = $value && isset($value[0]);
    
    if ($has_value)
    {
        $original = $value[0];        
        echo "<a target='_blank' href='".escape($original['url'])."'>".escape($original['filename'])."</a>";
    }
?></span>
<span id='upload_remove_span<?php echo $UPLOAD_INCLUDE_COUNT; ?>' <?php 
    if (!$has_value) { echo "style='display:none'"; } 
?> >
<input type='button' onclick='removeUpload<?php echo $UPLOAD_INCLUDE_COUNT ?>();' value='<?php echo escape(__('upload:remove')); ?>' />
</span>

<span id='upload_browse_span<?php echo $UPLOAD_INCLUDE_COUNT; ?>' <?php 
    if ($has_value) { echo "style='display:none'"; } 
?>>
<?php
    echo view('input/swfupload', array(
        'name' => $vars['name'],
        'id' => $resultId,
        'progressid' => $progressId,
        'value' => @$vars['value'],
        'swfupload_class' => 'FileUploader',
        'swfupload_args' => array(),
        'jsname' => 'uploader'
    ));
?>
</span>
<script type='text/javascript'>

    var uploader = window.uploader;
    
    uploader.showPreview = function($files, $json) 
    {
        var span = document.getElementById('upload_span<?php echo $UPLOAD_INCLUDE_COUNT; ?>');
        removeChildren(span);
        var link = document.createElement('a');
        link.target = "_blank";
        
        link.href = $files[0].url;
        link.appendChild(document.createTextNode($files[0].filename));
        span.appendChild(link);
            
        var removeSpan = document.getElementById('upload_remove_span<?php echo $UPLOAD_INCLUDE_COUNT; ?>');
        removeSpan.style.display = 'inline';

        var browseSpan = document.getElementById('upload_browse_span<?php echo $UPLOAD_INCLUDE_COUNT; ?>');
        browseSpan.style.display = 'none';        
        
        document.getElementById('<?php echo $resultId; ?>').value = $json;
    };
    
    function removeUpload<?php echo $UPLOAD_INCLUDE_COUNT ?>()
    {
        var removeSpan = document.getElementById('upload_remove_span<?php echo $UPLOAD_INCLUDE_COUNT; ?>');
        removeSpan.style.display = 'none';

        var browseSpan = document.getElementById('upload_browse_span<?php echo $UPLOAD_INCLUDE_COUNT; ?>');
        browseSpan.style.display = 'inline';
        
        var span = document.getElementById('upload_span<?php echo $UPLOAD_INCLUDE_COUNT; ?>');
        removeChildren(span);
        
        document.getElementById('<?php echo $resultId; ?>').value = '';
    }
    
    document.getElementById('<?php echo $progressId; ?>').style.display = 'inline';
</script>