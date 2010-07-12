<div class='padded section_content'>
<?php
    $widget = $vars['widget'];

    if (!$widget->content)
    {
        echo sprintf(elgg_echo('widget:empty'), escape(elgg_echo("widget:{$widget->widget_name}")));
    }
    else
    {
        echo $widget->renderContent();
    }

?>
<div style='clear:both'></div>
</div>