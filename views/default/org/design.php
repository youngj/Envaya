<?php
    global $CONFIG;
    $user = $vars['entity'];
?>

<form action='action/org/design' method='POST'>

<?php echo elgg_view('input/securitytoken'); ?>

<div class='section_header'><?php echo elgg_echo('header'); ?></div>
<div class='section_content padded'>

<script type='text/javascript'>

function getSelectedRadio($name)
{
    var $buttons = document.getElementsByName($name);
    for (var $i = 0; $i < $buttons.length; $i++)
    {
        var $button = $buttons[$i];
        if ($button.checked)
        {
            return $button.value;
        }
    }
    return $null;
}    

function customHeaderChanged()
{  
    setTimeout(function() {
        var $customDiv = document.getElementById('custom_header_container');
        var $defaultDiv = document.getElementById('default_header_container');
        var $value = getSelectedRadio('custom_header');

        if ($value == '1')
        {
            $customDiv.style.display = 'block';
            $defaultDiv.style.display = 'none';
        }
        else
        {
            $customDiv.style.display = 'none';
            $defaultDiv.style.display = 'block';
        }
    }, 1);    
}
</script>

<?php
    echo elgg_view('input/radio', array(
        'internalname' => 'custom_header',
        'value' => $user->custom_header ? '1' : '0',
        'js' => "onchange='customHeaderChanged()' onclick='customHeaderChanged()'",
        'options' => array(
            '0' => elgg_echo('header:default'),
            '1' => elgg_echo('header:custom'),
        )
    ));
?>

<div id='default_header_container' <?php echo $user->custom_header ? "style='display:none'" : "" ?> >
    <div class='header_preview'>
        <?php echo elgg_view('org/default_header', array('org' => $user, 'subtitle' => elgg_echo('header:subtitle'))) ?>
    </div>   
    <div class='help'><?php echo sprintf(elgg_echo('header:changelogo'), elgg_echo('icon')) ?></div>
</div>

<div id='custom_header_container' <?php echo !$user->custom_header ? "style='display:none'" : "" ?>>
    
    <?php 
        if ($user->custom_header)
        {
            echo "<div style='margin-top:10px'>".elgg_echo('image:current')."</div>";
            echo "<div class='header_preview'>".elgg_view('org/custom_header', array('org' => $user))."</div>";
        }    
    ?>

    <div class='input'>
            <?php 
                if ($user->custom_header)
                {
                    echo elgg_echo('image:new');
                }
                else
                {
                    echo elgg_echo('header:chooseimage');
                }    
            ?>    
        <br />
    <?php 

    echo elgg_view("input/swfupload_image", 
        array(
            'trackDirty' => true,
            'sizes' => ElggUser::getHeaderSizes(),
            'thumbnail_size' => 'large',
            'internalname' => 'header',                    
        )) 
    ?> 
    <div class='help'>
    <?php echo elgg_echo('header:constraints') ?>
    </div>
    </div>
</div>


<?php
echo elgg_view('input/submit',array(
    'value' => elgg_echo('savechanges'),
    'trackDirty' => true,
));

?>
   
</div>

<div class='section_header' id='icon'><?php echo elgg_echo('icon'); ?></div>
<div class='section_content padded'>
<div class='help' style='padding-bottom:5px'>
<?php echo elgg_echo('icon:description') ?>
</div>


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
