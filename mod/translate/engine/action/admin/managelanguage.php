<?php

class Action_Admin_ManageLanguage extends Action
{
    function before()
    {
        $this->require_admin();
    }
    
    function process_input()
    {
        $language = $this->param('language');
        if (get_input('delete'))
        {
            $language->disable();
            $language->save();
            
            SessionMessages::add(__('itrans:language_deleted'));
            $this->redirect('/tr/admin');
        }
        else
        {
            
            $language->name = get_input('name');
            $language->save();
            
            $available_groups = $language->get_available_groups();
            
            $enabled_groups = get_input_array('group_names');
            
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
                    $keys = $group->get_available_keys();
                    if ($group->get_defined_group())
                    {
                        foreach ($keys as $key)
                        {
                            $key->init_defined_translation();
                        }
                    }
                    $group->update();
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