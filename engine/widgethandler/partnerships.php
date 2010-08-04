<?php

class WidgetHandler_Partnerships extends WidgetHandler
{
    function view($widget)
    {
        return view("widgets/partnerships_view", array('widget' => $widget));
    }

    function edit($widget)
    {
        return view("widgets/partnerships_edit", array('widget' => $widget));
    }

    function save($widget)
    {
        $org = $widget->getContainerEntity();
        $partnerships = $org->queryPartnerships()->filter();

        foreach($partnerships as $p)
        {
            $p->description = get_input("partnershipDesc{$p->guid}");
            $p->save();
        }
        $widget->save();
    }
}

