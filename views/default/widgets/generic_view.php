<div class='padded section_content'>
<?php
    $widget = $vars['widget'];

    if (!$widget->content)
    {
        echo sprintf(__('widget:empty'), escape($widget->get_title()));
    }
    else
    {
        echo $widget->render_content();
    }

?>
<div style='clear:both'></div>
</div>