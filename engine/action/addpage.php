<?php

class Action_AddPage extends Action
{
    function before()
    {
        $this->require_editor();
        $this->require_org();        
    }
     
    function process_input()
    {
        $this->validate_security_token();
        
        $org = $this->get_org();
        
        $title = get_input('title');
        if (!$title)
        {
            register_error(__('widget:no_title'));            
            return $this->render();
        }
        
        $widget_name = get_input('widget_name');
        if (!$widget_name || !Widget::is_valid_name($widget_name))
        {
            register_error(__('widget:bad_name'));            
            return $this->render();
        }
        
        $widget = $org->get_widget_by_name($widget_name);
        
        if ($widget->guid && ((time() - $widget->time_created > 30) || !($widget->get_handler() instanceof WidgetHandler_Generic)))
        {
            register_error_html(sprintf(__('widget:duplicate_name'),"<a href='{$widget->get_edit_url()}'><strong>".__('clickhere')."</strong></a>")); 
            return $this->render();
        }
        
        $draft = (int)get_input('_draft');
        if ($draft)
        {
            $widget->set_status(EntityStatus::Draft);
        }
        
        $widget->save_input();             
        
        system_message(__('widget:save:success'));
        
        if ($draft)
        {
            forward($widget->get_edit_url());        
        }
        else
        {
            forward($widget->get_url());        
        }
    }

    function render()
    {
        $org = $this->get_org();
        
        $cancelUrl = get_input('from') ?: $org->get_url();
        PageContext::add_submenu_item(__("canceledit"), $cancelUrl, 'edit');
        
        $title = __("widget:new");
        
        $area1 = view("widgets/add", array('org' => $org));
        $body = view_layout("one_column_padded", view_title($title), $area1);

        $this->page_draw($title,$body);     
    }
    
}    