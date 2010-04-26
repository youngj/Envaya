<?php
    $previewUrl = $vars['previewUrl'];
    $name = $vars['internalname'];
    
    $curTheme = restore_input($vars['internalname'], @$vars['value']); 
?>

<script type='text/javascript'>

function previewTheme($theme)
{
    var iframe = document.getElementById('previewFrame');
    iframe.src = <?php echo json_encode($previewUrl) ?> + "?__topbar=0&__readonly=1&__theme=" + $theme;
}

</script>

<?php

foreach (get_themes() as $theme)
{
    $selected = ($theme == $curTheme) ? "checked='checked'" : '';
    $label = elgg_echo("theme:$theme");
    echo "<label class='optionLabel'><input type='radio' onclick='previewTheme(\"".escape($theme)."\")' name='$name' value='".escape($theme)."' {$selected} class='input-radio' />{$label}</label><br />";
}
?>

<div class='help'><?php echo elgg_echo('preview'); ?>:</div>

<div style='width:458px;height:298px;overflow:hidden;border:1px solid black'>
<iframe width='700' height='298' scrolling='no' id='previewFrame' src="<?php echo $previewUrl ?>?__theme=<?php echo escape($curTheme) ?>&__topbar=0&__readonly=1"></iframe>
</div>
