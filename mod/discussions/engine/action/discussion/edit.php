<?php

class Action_Discussion_Edit extends Action
{
    function before()
    {    
        Permission_EditUserSite::require_for_entity($this->get_user());
    }
     
    function process_input()
    {
        $topic = $this->get_topic();
        $user = $this->get_user();
        
        if (get_input('delete'))
        {
            $topic->disable();
            $topic->save();
            SessionMessages::add(__('discussions:topic_deleted'));            
            
            $widget = Widget_Discussions::get_or_new_for_entity($user);
            $this->redirect($widget->get_edit_url());
        }
        else
        {        
            $subject = get_input('subject');
            if (!$subject)
            {
                throw new ValidationException(__('discussions:subject_missing'));
            }
            
            $topic->subject = $subject;            
            $topic->save();
            $topic->queue_guess_language('subject');                        
            
            SessionMessages::add(__('discussions:topic_saved'));                    
            $this->redirect($topic->get_edit_url());   
        }
    }

    function render()
    {
        $topic = $this->get_topic();
        $this->use_editor_layout();
        
        $cancelUrl = get_input('from') ?: $topic->get_url();
        PageContext::get_submenu('top')->add_link(__("canceledit"), $cancelUrl);
                
        $this->page_draw(array(
            'title' => __('discussions:edit_topic'),
            'content' => view("discussions/topic_edit", array('topic' => $topic))
        ));        
    }
}    