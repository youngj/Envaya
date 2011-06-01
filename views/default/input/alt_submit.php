<?php
     /*
        Allows you to determine which submit button was clicked in IE6.
        Also uses type='button' instead of type='submit', so that the enter key doesn't press it unless it is focused.
     */
     
    $track_dirty = false;
    $confirm = null;
    $attrs = array();    
    extract($vars);
     
    if ($INCLUDE_COUNT == 0)
    {
        ob_start();
        ?>
<script type='text/javascript'>
function altSubmit($id, $value)
{
    var hiddenField = $($id);
    hiddenField.value = $value;
    for (var i = 0; i < document.forms.length; i++)
    {
        var form = document.forms[i];
        if (form[hiddenField.name] == hiddenField)
        {
            form.submit();
            return true;
        }        
    }    
    document.forms[0].submit();
    return true;
}
</script>
        <?php
        $script = ob_get_clean();
        PageContext::add_header_html($script);
    }

    $hidden_id = "_alt_submit".$INCLUDE_COUNT;

    echo view('input/hidden', array(
        'name' => $vars['name'],
        'id' => $hidden_id,
        'value' => '',
    ));

    $js = "return true ";

    if ($confirm)
    {
        $js .= "&& confirm(".json_encode($confirm).")";
    }
    if ($track_dirty)
    {
        $js .= "&& setSubmitted() ";
    }
    $js .= "&& altSubmit(".json_encode($hidden_id).",1);";
    
    $attrs['onclick'] = $js;
    
    $vars['name'] = "_alt_submit";
    $vars['attrs'] = $attrs;
    $vars['type'] = 'button'; // not 'submit' so that enter key doesn't automatically click this button
    $vars['track_dirty'] = false;
	    
    echo view('input/button', $vars);
