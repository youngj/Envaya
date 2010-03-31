<?php
    global $CONFIG;
    $user = page_owner_entity();
    
    if ($user && $user instanceof Organization) 
    {
?>

    <div class='section_header'>    
        <?php echo elgg_echo('theme') ?>
    </div>
    <div class='section_content'>

<script type='text/javascript'>

function previewTheme($theme)
{
    var iframe = document.getElementById('previewFrame');
    iframe.src = <?php echo json_encode($user->getURL()) ?> + "?__topbar=0&__theme=" + $theme;
}

</script>

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

<label><?php echo elgg_echo('preview'); ?>:</label>
</div>

<div style='position:relative;width:500px;height:300px'>    
    <iframe width='458' height='298' style='position:absolute;left:0px;top:0px;border:1px solid black' scrolling='no' id='previewFrame' src="<?php echo $user->getURL() ?>?__topbar=0"></iframe>
    <div style='position:absolute;background-color:white;width:460px;height:300px;left:0px;top:0px;opacity:0.01;filter:alpha(opacity=1)'></div>
</div>    

<?php echo elgg_view('input/submit', array('value' => elgg_echo('savechanges'), 'trackDirty' => true)); ?>

</div>

&nbsp;
    </div>

    <div class='section_header'><?php echo elgg_echo('org:icon'); ?></div>
    <div class='section_content padded'>
    
        <?php
        
            echo elgg_view("input/image", 
                array(
                    'current' => $user->getIcon('medium'),
                    'removable' => $user->custom_icon,
                    'internalname' => 'icon',
                    'deletename' => 'deleteicon',
                )) 
        
         ?> 
         
<?php echo elgg_view('input/submit', array('value' => elgg_echo('savechanges'), 'trackDirty' => true)); ?>         

    </div>

<?php } ?>