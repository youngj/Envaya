<div class='padded section_content'>
<?php
    $widget = $vars['widget'];

    if (!$widget->content)
    {
        echo sprintf(__('widget:empty'), escape($widget->getTitle()));
    }
    else
    {
        echo $widget->renderContent();
    }

?>
<div style='clear:both'></div>
</div>