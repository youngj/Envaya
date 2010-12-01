<?php
$field_name = $vars['field_name'];
$auto_value = $vars['auto_value'];
$auto_update = $vars['auto_update'];

if ($auto_value) 
{
    echo "<script type='text/javascript'>";
    echo "autoFunctions[".json_encode($field_name)."] = function() { return $auto_value; };\n";
    echo "</script>";
}

if ($auto_update)
{
    echo "<script type='text/javascript'>";
    echo "setTimeout(function() { 
        addEvent(document.forms[0]['field_' + ".json_encode($field_name)."], 'keyup', 
            function() { updateValue(".json_encode($auto_update)."); }
        );
    });\n";    
    echo "</script>";    
}

?>