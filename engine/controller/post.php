<?php

class Controller_Post extends Controller_Widget
{   
    function action_preview()
    {
        $this->set_content_type('text/javascript');
        $this->set_response(json_encode($this->widget->js_properties()));
    }

    function action_prev()
    {
        $this->redirect_delta(-1);
    }

    function action_next()
    {
        $this->redirect_delta(1);
    }

    function redirect_delta($delta)
    {
        $widget = $this->widget;

        $op = ($delta > 0) ? ">" : "<";
        $order = ($delta > 0) ? "asc" : "desc";
        
        $container = $widget->get_container_entity();

        $sibling = $container->query_widgets()
            ->where('status = ?', EntityStatus::Enabled)
            ->where("guid $op ?", $widget->guid)
            ->order_by("guid $order")
            ->get();
        
        if ($sibling)
        {
            forward($sibling->get_url());
        }
        
        $sibling = $container->query_widgets()
            ->where('status = ?', EntityStatus::Enabled)
            ->order_by("guid $order")
            ->get();        

        if ($sibling)
        {
            forward($sibling->get_url());
        }
    }
}