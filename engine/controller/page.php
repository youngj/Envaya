<?php

/*
 * Controller for top-level widgets (pages) on a user's site,
 * keyed by widget_name
 *
 * URL: /<username>/page/<widget_name>[/<action>]
 */
class Controller_Page extends Controller_Widget
{       
    static $routes = array(
        array(
            'regex' => '/(?P<widget_name>[\w\-]+)(/(?P<action>\w+)\b)?',
            'defaults' => array('action' => 'index'),
            'before' => 'init_widget_from_name',
        ),
    );

    protected function init_widget_from_name()
    {    
        $widgetName = $this->param('widget_name');        
        $this->init_widget($this->get_user()->get_widget_by_name($widgetName)); 
    }
}