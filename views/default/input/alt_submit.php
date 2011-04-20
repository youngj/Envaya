<?php
     /*
        Allows you to determine which submit button was clicked in IE6.
        Also uses type='button' instead of type='submit', so that the enter key doesn't press it unless it is focused.
     */
     
    if ($vars['include_count'] == 0)
    {
        ob_start();
        ?>
<script type='text/javascript'>
function altSubmit($id, $value)
{
    var hiddenField = document.getElementById($id);
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
            PageContext::add_header_html('alt_submit', $script);
    }

    $hidden_id = "_alt_submit".$vars['include_count'];

    echo view('input/hidden', array(
        'name' => $vars['name'],
        'id' => $hidden_id,
        'value' => '',
    ));

    $js = "onclick='return true ";

    if (@$vars['confirmMessage'])
    {
        $js .= "&& confirm(".json_encode(@$vars['confirmMessage']).")";
    }
    if (@$vars['trackDirty'])
    {
        $js .= "&& setSubmitted() ";
    }
    $js .= "&& altSubmit(".json_encode($hidden_id).",1);'";
	    
    echo view('input/button', array(
        'name' => "_alt_submit",
        'id' => @$vars['id'],
        'js' => $js,
        'trackDirty' => @$vars['trackDirty'],
        'type' => 'button', // not 'submit' so that enter key doesn't automatically click this button
        'class' => @$vars['class'] ?: "submit_button",
        'value' => @$vars['value']
    ));

?>