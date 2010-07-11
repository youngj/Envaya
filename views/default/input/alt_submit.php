<script type='text/javascript'>
function setHiddenSubmit($id)
{
    document.getElementById($id).value = "1";
    return true;
}
</script>
<?php

     /*
        Allows you to determine which submit button was clicked in IE6
     */

    $hidden_id = "_alt_submit".mt_rand();

    echo elgg_view('input/hidden', array(
        'internalname' => $vars['internalname'],
        'internalid' => $hidden_id,
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
    $js .= "&& setHiddenSubmit(".json_encode($hidden_id).");'";

    echo elgg_view('input/submit', array(
        'internalname' => "_alt_submit",
        'internalid' => @$vars['internalid'],
        'js' => $js,
        'trackDirty' => @$vars['trackDirty'],
        'class' => @$vars['class'],
        'value' => @$vars['value']
    ));

?>