<?php
class WidgetHandler_Generic extends WidgetHandler
{
    function view($widget)
    {
        return view("widgets/generic_view", array('widget' => $widget));
    }

    function edit($widget)
    {
        return view("widgets/generic_edit", array('widget' => $widget));
    }

    function save($widget)
    {
        $prevContent = $widget->content;

        $title = get_input('title');
        if ($title)
        {
            $widget->title = $title;
        }
        
        $widget->setContent(get_input('content'), true);
        $widget->save();

        if (!$prevContent && $widget->content)
        {
            post_feed_items($widget->getContainerEntity(), 'new_widget', $widget);
        }
    }
}

