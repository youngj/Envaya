<?php
    global $CONFIG;
    $user = $vars['entity'];
?>

<form action='action/org/design' method='POST'>

<?php echo elgg_view('input/securitytoken'); ?>

<div class='section_header'><?php echo elgg_echo('icon'); ?></div>
<div class='section_content padded'>

<!--
<div class='instructions'>
<?php echo elgg_echo('icon:description') ?>
</div>
-->


<?php

echo elgg_view("input/image", 
    array(
        'current' => $user->getIcon('medium'),
        'trackDirty' => true,
        'sizes' => ElggUser::getIconSizes(),
        'removable' => $user->custom_icon,
        'thumbnail_size' => 'medium',
        'internalname' => 'icon',                    
        'deletename' => 'deleteicon',
    )) 

?> 

<?php
echo elgg_view('input/submit',array(
    'value' => elgg_echo('savechanges'),
    'trackDirty' => true,
));

?>

</div>

<div class='section_header'><?php echo elgg_echo("theme"); ?></div>
<div class='section_content padded'>

<script type='text/javascript'>

function previewTheme($theme)
{
var iframe = document.getElementById('previewFrame');
iframe.src = <?php echo json_encode($user->getURL()) ?> + "?__topbar=0&__readonly=1&__theme=" + $theme;
}

</script>

<div class='input'>
<?php
$curTheme = $user->theme;        

foreach (get_themes() as $theme)
{
    $selected = ($theme == $curTheme) ? "checked='checked'" : '';
    $label = elgg_echo("theme:$theme");
    echo "<label class='optionLabel'><input type='radio' onclick='previewTheme(\"".escape($theme)."\")' name='theme' value='".escape($theme)."' {$selected} class='input-radio' />{$label}</label><br />";
}
?>

<label><?php echo elgg_echo('preview'); ?>:</label>
</div>

<iframe width='458' height='298' style='border:1px solid black' scrolling='no' id='previewFrame' src="<?php echo $user->getURL() ?>?__topbar=0&__readonly=1"></iframe>

<?php 
echo elgg_view('input/hidden', array('internalname' => 'guid', 'value' => $user->guid));

echo elgg_view('input/submit',array(
    'value' => elgg_echo('savechanges'),
    'trackDirty' => true,
));
?>
</div>
</form>
