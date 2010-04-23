<div class='padded section_content'>
<?php
    $widget = $vars['widget'];
    
    if ($widget->hasImage())
    {
        $imagePos = $widget->image_position;
        $imageSize = ($imagePos == 'left' || $imagePos == 'right') ? 'medium' : 'large';
        
        $img = "<img class='widget_image_".escape($imagePos)."' src='{$widget->getImageUrl($imageSize)}' />";                    
        
        if ($imagePos != 'bottom')
        {
            echo $img;
        }
    }    
    else if (!$widget->content)
    {
        echo sprintf(elgg_echo('widget:empty'), escape(elgg_echo("widget:{$widget->widget_name}")));
    }
    
    echo elgg_view('output/longtext', array('value' => translate_field($widget, 'content'))); 
    
    if ($widget->hasImage() && $imagePos == 'bottom')
    {
        echo $img;
    }
?>
<div style='clear:both'></div>
</div>