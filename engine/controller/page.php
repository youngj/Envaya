<?php

/*
 * Controller for top-level widgets (pages) on a user's site,
 * keyed by widget_name
 *
 * URL: /<username>/page/<widget_name>[/<action>]
 */
class Controller_Page extends Controller_Widget
{       
    protected function init_widget()
    {    
        $widgetName = $this->request->param('id');        
        $this->widget = $this->org->get_widget_by_name($widgetName); 
    }
}