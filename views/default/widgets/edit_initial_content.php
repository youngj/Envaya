<script type='text/javascript'>
function saveInitialDraft()
{
    $('save_message').style.display='inline';
    setSubmitted();
    var form = document.forms[0];
    form._draft.value = '1';
    form.submit();
}

</script>
<?php 
    echo view('input/hidden', array('name' => '_draft'));    
    $vars['name'] = 'content';
    $vars['saveFn'] = 'saveInitialDraft';
    $vars['trackDirty'] = true;    
    echo view('input/tinymce', $vars);
?>
<span id='save_message' style='display:none;font-weight:bold;display:none;position:absolute;left:10px;top:40px;color:white;'><?php echo __('tinymce:saving'); ?></span>
