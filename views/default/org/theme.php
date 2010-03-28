<?php
    $user = $vars['entity'];
?>

<script type='text/javascript'>

function previewTheme($theme)
{
    var iframe = document.getElementById('previewFrame');
    iframe.src = <?php echo json_encode($user->getURL()) ?> + "?__topbar=0&__theme=" + $theme;
}

</script>

<form action='action/org/theme' method='POST'>

<?php echo elgg_view('input/securitytoken'); ?>

<div class='padded'>
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
</div>

</div>

<div style='position:relative;width:500px;height:400px'>    
    <iframe width='498' height='398' style='position:absolute;left:-10px;top:0px;border:1px solid black' scrolling='no' id='previewFrame' src="<?php echo $user->getURL() ?>?__topbar=0"></iframe>
    <div style='position:absolute;background-color:white;width:500px;height:400px;left:-10px;top:0px;opacity:0.01;filter:alpha(opacity=1)'></div>
</div>    

<div class='padded'>

<?php 
echo elgg_view('input/hidden', array('internalname' => 'guid', 'value' => $user->guid));

echo elgg_view('input/submit',array(
    'value' => elgg_echo('save')
));
?>
</div>
</form>
