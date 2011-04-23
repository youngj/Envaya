<?php

class Action_Discussion_Edit extends Action
{
    function before()
    {
        $this->require_editor();
    }
     
    function process_input()
    {
        $topic = $this->get_topic();
        $org = $this->get_org();
        
        if (get_input('delete'))
        {
            $topic->disable();
            $topic->save();
            SessionMessages::add(__('discussions:topic_deleted'));            
            
            $widget = $org->get_widget_by_class('Discussions');
            forward($widget->get_edit_url());
        }

        $subject = get_input('subject');
        if (!$subject)
        {
            return SessionMessages::add_error(__('discussions:subject_missing'));
        }
        
        $topic->subject = $subject;
        $topic->save();
        
        SessionMessages::add(__('discussions:topic_saved'));                    
        forward($topic->get_edit_url());   
    }

    function render()
    {
        $topic = $this->get_topic();
        $this->use_editor_layout();
        
        $cancelUrl = get_input('from') ?: $topic->get_url();
        PageContext::get_submenu('edit')->add_item(__("canceledit"), $cancelUrl);
                
        $this->page_draw(array(
            'title' => __('discussions:edit_topic'),
            'content' => view("discussions/topic_edit", array('topic' => $topic))
        ));        
    }
}    