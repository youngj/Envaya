<?php

abstract class Action_ContactTemplate_Add extends Action
{
    function before()
    {
        Permission_SendMessage::require_for_root();
    }
    
    abstract function init_template($template);
     
    function process_input()
    {
        $template_class = $this->controller->template_class;
        
        $template = new $template_class();
        $template->filters_json = get_input('filters_json');
        
        $this->init_template($template);
        
        $template->set_owner_entity(Session::get_logged_in_user());
        
        $template->save();
        $this->redirect($template->get_url());
    }

    function render()
    {        
        $template_class = $this->controller->template_class;
        $template = new $template_class();
        $template->set_filters(array(
            new Query_Filter_User_Type(array('value' => Organization::get_subtype_id())),
            new Query_Filter_User_Approval(array('value' => User::Approved))
        ));
    
        $this->page_draw(array(
            'title' => sprintf(__('contact:add_template'), $this->get_type_name()),
            'header' => $this->get_header(array(
                'template' => null,
                'title' => __('add')
            )),                        
            'content' => view($this->controller->add_view, array(
                'template' => $template
            )),
        ));
    }
}