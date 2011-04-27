<?php

class Action_Admin_ManageLanguage extends Action
{
    protected $language;

    function before()
    {
        $this->require_admin();
        
        $lang = $this->param('lang');
        if (!$lang)
        {
            return $this->not_found();
        }
        
        $language = InterfaceLanguage::query()
            ->where('code = ?', $lang)
            ->show_disabled(true)
            ->get();
            
        if (!$language)
        {    
            $language = new InterfaceLanguage();
            $language->code = $lang;
        }
        $this->language = $language;        
    }
    
    function process_input()
    {
        $language = $this->language;
        if (get_input('delete'))
        {
            $language->disable();
            $language->save();
            
            SessionMessages::add(__('itrans:language_deleted'));
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
        }
        forward("/tr/admin");
    }
    
    function render()
    {
        $language = $this->language;
        $this->page_draw(array(
            'title' => __('itrans:manage'),
            'header' => view('translate/admin/header', array('items' => array($language))),
            'content' => view('translate/admin/manage_language', array('language' => $language))
        ));
    }
}