<?php

	/**
	 * A password input field
	 */

    $name = null;            // html name attribute for input field
    $value = null;           // html value attribute
    $track_dirty = false;     // call setDirty when the field is changed?    
    extract($vars);
    
    $attrs = Markup::get_attrs($vars, array(
        'type' => 'password',
        'class' => 'input-password',
        'name' => null,
        'style' => null,
        'id' => null,
    ));

    $attrs['value'] = restore_input($name, $value, $track_dirty); 

    if ($track_dirty)
    {
        $attrs['onkeyup'] = $attrs['onchange'] = "setDirty(true)";
    }               
    
    if (!$INCLUDE_COUNT)
    {
?>

<script type='text/javascript'>
function checkCapslock(e, warningId) {
    var ev = e || window.event;
    if (ev) {
        var target = ev.target ? ev.target : ev.srcElement;
        var which = ev.which || ev.keyCode;
        var shiftPressed = ev.shiftKey || (ev.modifiers ? !!(ev.modifiers & 4) : false);
        
        var warning = $(warningId);
        
        if (((which >= 65 && which <=  90) && !shiftPressed) ||
            ((which >= 97 && which <= 122) && shiftPressed)) {
          warning.style.display = 'inline';
        } else {
          warning.style.display = 'none';
        }
    }
} 
</script>

<?php
    }
    
    $warningId = "capslockWarning$INCLUDE_COUNT";
    $attrs['onkeypress'] = "checkCapslock(event,'$warningId')";
    
    echo "<input ".Markup::render_attrs($attrs)." />";
?>

<span class='capslockWarning' id='<?php echo $warningId ?>' style='display:none'></span>
<script type='text/javascript'>
$('<?php echo $warningId; ?>').innerHTML = <?php echo json_encode(__('capslock_warning')); ?>;
</script>