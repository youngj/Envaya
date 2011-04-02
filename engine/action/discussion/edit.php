<?php

class Action_Discussion_Edit extends Action
{
    function before()
    {
        $this->require_editor();
    }
     
    function process_input()
    {
        $this->validate_security_token();        
        $topic = $this->get_topic();
        $org = $this->get_org();
        
        if (get_input('delete'))
        {
            $topic->disable();
            $topic->save();
            system_message(__('discussions:topic_deleted'));            
            
            $widget = $org->get_widget_by_class('WidgetHandler_Discussions');
            forward($widget->get_edit_url());
        }

        $subject = get_input('subject');
        if (!$subject)
        {
            return register_error(__('discussions:subject_missing'));
        }
        
        $topic->subject = $subject;
        $topic->save();
        
        system_message(__('discussions:topic_saved'));                    
        forward($topic->get_edit_url());   
    }

    function render()
    {
        $topic = $this->get_topic();
        $this->use_editor_layout();
        
        $title = __('discussions:edit_topic');
        
        $cancelUrl = get_input('from') ?: $topic->get_url();
        PageContext::add_submenu_item(__("canceledit"), $cancelUrl, 'edit');
                
        $body = view_layout('one_column', view_title($title), view("discussions/topic_edit", array('topic' => $topic)));
        
        $this->page_draw($title, $body);        
    }
}    