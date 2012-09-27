<?php

class Action_Admin_ManageLanguage extends Action
{
    function before()
    {
        Permission_ManageLanguage::require_for_entity($this->param('language'));
    }
    
    function process_input()
    {
        $language = $this->param('language');
        if (Input::get_string('delete'))
        {
            $language->disable();
            $language->save();
            
            SessionMessages::add(__('itrans:language_deleted'));
            $this->redirect('/tr/admin');
        }
        else
        {
            
            $language->name = Input::get_string('name');
            $language->enable();
            $language->save();
            
            $available_groups = $language->get_available_groups();
            
            $enabled_groups = Input::get_array('group_names');
            
            $disabled_groups = $language->query_groups()->where_not_in('name', $enabled_groups)->filter();
            foreach ($disabled_groups as $disabled_group)
            {
                $disabled_group->disable();
                $disabled_group->save();
            }
            
            foreach ($available_groups as $group)
            {
                if (in_array($group->name, $enabled_groups))
                {
                    $group->status = Entity::Enabled;
                    $group->save();                    
                    $group->update_defined_translations();
                }
            }
            
            SessionMessages::add(__('itrans:language_saved'));            
            $this->redirect($language->get_url());
        }        
    }
    
    function render()
    {
        $language = $this->param('language');
        $this->page_draw(array(
            'title' => __('itrans:manage'),
            'header' => view('translate/admin/header', array('items' => array($language))),
            'content' => view('translate/admin/manage_language', array('language' => $language))
        ));
    }
}