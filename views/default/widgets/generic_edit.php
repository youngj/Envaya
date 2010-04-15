<div class='padded section_content'>
<?php

    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();
    

    ob_start();    
?>
<script>
function togglePosition()
{
    var upload = document.getElementById('imageUpload');
    var position = document.getElementById('imagePosition');
    var deleteImage = document.getElementById('imageDelete');
    var show = upload.value || (deleteImage && !deleteImage.checked);    
    position.style.display = show ? 'block' : 'none';
}
</script>

<div class='input'>
    <label><?php echo elgg_echo('widget:image:label') ?></label><br />        
    <?php echo elgg_view('input/image', array(
        'current' => ($widget->hasImage() ? $widget->getImageUrl('small') : null),
        'js' => "onchange='javascript:togglePosition()'",
        'internalname' => 'image',
        'sizes' => Widget::getImageSizes(),
        'internalid' => 'imageUpload',
        'deletename' => 'deleteimage',
        'deleteid' => 'imageDelete',         
    )) ?>
</div>

<div id='imagePosition' <?php echo ($widget->hasImage() ? '' : 'style="display:none"') ?> class='input'>
    <label><?php echo elgg_echo('widget:image:position') ?></label><br />
        <?php echo elgg_view("input/radio",array(
            'internalname' => 'image_position',
            'inline' => true,
            'value' => $widget->image_position ?: 'left',
            'options' => array(
                'left' => elgg_echo('position:left'),    
                'top' => elgg_echo('position:top'),
                'right' => elgg_echo('position:right'),
                'bottom' => elgg_echo('position:bottom')
             ),
        )); ?>
</div>           


<div class='input'>
    <label><?php echo elgg_echo("widget:{$widget->widget_name}:label") ?></label>
    <?php
        $helpCode = "widget:{$widget->widget_name}:help";
        $help = elgg_echo($helpCode);
        if ($help != $helpCode)
        {
            echo "<div class='help'>$help</div>";
        }
        else
        {
            echo "<br />";
        }    
    ?>
    <?php echo elgg_view("input/longtext", array('internalname' => 'content', 
        'trackDirty' => true,
        'value' => $widget->content)); ?>                   

</div>
<?php
    $content = ob_get_clean();
    
    echo elgg_view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));
    
?>
</div>