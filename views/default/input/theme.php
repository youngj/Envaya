<?php
    $previewUrl = $vars['previewUrl'];
    $name = $vars['name'];
    $curTheme = restore_input($vars['name'], @$vars['value']);
    
    $theme_names = Theme::available_names();
    $itemWidth = 150;
?>

<script type='text/javascript'>

function selectTheme(themeName)
{
    $('theme').value = themeName;
    var links = $('themes').getElementsByTagName('a');
    for (var i = 0; i < links.length; i++)
    {
        var span = links[i].getElementsByTagName('span')[0];
        span.style.color = '';
        span.style.fontWeight = '';
    }
    var link = $('theme_' + themeName);
    if (link)
    {
        var span = link.getElementsByTagName('span')[0];
        span.style.color = 'black';
        span.style.fontWeight = 'bold';        
    }
}

function previewTheme()
{
    var $theme = $('theme').value;    
    var win = window.open(<?php echo json_encode($previewUrl); ?> + "?__theme="+$theme+"&__topbar=0&__readonly=1&view=default",
        'themePreview', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=730,height=590'
    );
    if (window.focus)
    {
        win.focus();
    }
}

</script>

<?php

    $optionsValues = array();

    foreach ($vars['options'] as $theme)
    {
        $optionsValues[$theme] = __("design:theme:$theme");
    }

    echo view('input/hidden', array(
        'name' => $name,
        'id' => 'theme',
        'value' => $curTheme,
    ));        
    
    //style='width:100%;height:190px;overflow:auto'
    //style='width:px'
?>

<div id='themes'>
<?php   
    foreach ($theme_names as $theme_name)
    {
        $theme = Theme::get($theme_name);
        
        echo "<a id='theme_{$theme_name}' href='javascript:selectTheme(\"$theme_name\");' onclick='ignoreDirty()'             style='text-align:center;float:left;width:150px;padding-right:3px;display:block;width:{$itemWidth}px;height:170px;padding-bottom:12px'>";
        
        echo "<span>".$theme->get_display_name()."</span>";
        $thumbnail = $theme->get_thumbnail();
        if ($thumbnail)
        {
            echo "<img src='{$thumbnail}' style='display:block;border:1px solid #666;width:150px;height:150px' />";
        }        
        echo "</a>";
    }    
?>
</div>
<script type='text/javascript'>
selectTheme(<?php echo json_encode($curTheme) ?>);
</script>
<div style='clear:both'>
<!--
<a href='javascript:previewTheme()' onclick='ignoreDirty()'><?php echo __('preview'); ?></a>
-->
</div>
