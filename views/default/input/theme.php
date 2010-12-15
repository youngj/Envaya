<?php
    $previewUrl = $vars['previewUrl'];
    $name = $vars['internalname'];

    $curTheme = restore_input($vars['internalname'], @$vars['value']);
?>

<script type='text/javascript'>

function themeChanged($theme)
{
    setTimeout(function() {
        var $themeList = document.getElementById('themeList');

        var $theme = $themeList.options[$themeList.selectedIndex].value;

        var iframe = document.getElementById('previewFrame');
        iframe.src = <?php echo json_encode($previewUrl) ?> + "?__topbar=0&__readonly=1&view=default&__theme=" + $theme;

    }, 1);
}

</script>

<?php

    $optionsValues = array();

    foreach ($vars['options'] as $theme)
    {
        $optionsValues[$theme] = __("theme:$theme");
    }

     echo view('input/pulldown', array(
        'internalname' => $name,
        'internalid' => 'themeList',
        'options' => $optionsValues,
        //'empty_option' => __('sector:empty_option'),
        'value' => $curTheme,
        'js' => "onchange='themeChanged()' onkeypress='themeChanged()'"
    ));
?>

<div class='help'><?php echo __('preview'); ?>:</div>
<div style='width:100%;height:258px;overflow:hidden;border:1px solid black'>
<iframe width='800' height='258' scrolling='no' id='previewFrame' src="<?php echo $previewUrl ?>?__theme=<?php echo escape($curTheme) ?>&__topbar=0&__readonly=1&view=default"></iframe>
</div>
