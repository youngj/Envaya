<?php
    $id = "sms_content{$INCLUDE_COUNT}";
    $length_id = "sms_length{$INCLUDE_COUNT}";
    $vars['id'] = $id;
    $vars['style'] = 'height:80px';
    $vars['attrs'] = array(
        'onkeypress' => "updateSMSLength$INCLUDE_COUNT();trackDirty(event)",
        'onchange' => "updateSMSLength$INCLUDE_COUNT()",
    );       
    $vars['track_dirty'] = false;
?>
<script type='text/javascript'>
function updateSMSLength<?php echo $INCLUDE_COUNT; ?>()
{
    $('<?php echo $length_id; ?>').innerHTML = $('<?php echo $id; ?>').value.length;
}    
</script>
<?php
    echo view('input/longtext', $vars);
    echo "<div>(<span id='{$length_id}'>".strlen($vars['value'])."</span> characters)</div>";
?>
