<?php

	/**
	 * Displays a password input field
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['name'] The name of the input field
	 * 
	 */

	$class = @$vars['class'] ?: "input-password";

    $setDirty = (@$vars['trackDirty']) ? " onchange='setDirty(true)'" : "";
    
    $value = restore_input($vars['name'], @$vars['value'], @$vars['trackDirty']); 
    
    if (!$INCLUDE_COUNT)
    {
?>

<script type='text/javascript'>
<!--
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
// -->
</script>

<?php
    }
    
    $warningId = "capslockWarning$INCLUDE_COUNT";
    $js = @$vars['js'] ?: '';
    $js .= " onkeypress='checkCapslock(event,\"$warningId\")'";

?>

<input type="password" <?php if (@$vars['disabled']) echo ' disabled="yes" '; ?> <?php echo $js, $setDirty; ?> name="<?php echo $vars['name']; ?>" <?php if (isset($vars['id'])) echo "id=\"{$vars['id']}\""; ?> value="<?php echo escape($value); ?>" class="<?php echo $class; ?>" /><span class='capslockWarning' id='<?php echo $warningId ?>' style='display:none'></span>
<script type='text/javascript'>
<!--
$('<?php echo $warningId; ?>').innerHTML = <?php echo json_encode(__('capslock_warning')); ?>;
// -->
</script>