<?php

	/**
	 * Elgg password input
	 * Displays a password input field
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['internalname'] The name of the input field
	 * 
	 */

	$class = @$vars['class'] ?: "input-password";
        
    global $PASSWORD_INCLUDE_COUNT;
    
    $value = restore_input($vars['internalname'], @$vars['value']); 
    
    if (!isset($PASSWORD_INCLUDE_COUNT))
    {
        $PASSWORD_INCLUDE_COUNT = 0;
?>

<script type='text/javascript'>
function checkCapslock(e, warningId) {
    var ev = e || window.event;
    if (ev) {
        var target = ev.target ? ev.target : ev.srcElement;
        var which = ev.which || ev.keyCode;
        var shiftPressed = ev.shiftKey || (ev.modifiers ? !!(ev.modifiers & 4) : false);
        
        var warning = document.getElementById(warningId);
        
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
    else
    {
        $PASSWORD_INCLUDE_COUNT++;
    }
    
    $warningId = "capslockWarning$PASSWORD_INCLUDE_COUNT";
    $js = @$vars['js'] ?: '';
    $js .= " onkeypress='checkCapslock(event,\"$warningId\")'";

?>

<input type="password" <?php if (@$vars['disabled']) echo ' disabled="yes" '; ?> <?php echo $js; ?> name="<?php echo $vars['internalname']; ?>" <?php if (isset($vars['internalid'])) echo "id=\"{$vars['internalid']}\""; ?> value="<?php echo escape($value); ?>" class="<?php echo $class; ?>" /><span class='capslockWarning' id='<?php echo $warningId ?>' style='display:none'><?php echo __('capslock_warning'); ?></span>