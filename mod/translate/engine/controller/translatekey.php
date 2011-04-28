<?php

class Controller_TranslateKey extends Controller
{
    static $routes = array(
        array(
            'regex' => '(/)?$', 
        ),
        array(
            'regex' => '/(?P<translation_guid>\d+)/delete\b', 
            'defaults' => array('action' => 'delete_translation'),
            'before' => 'init_translation',
        ),                
        array(
            'regex' => '/(?P<translation_guid>\d+)/vote\b', 
            'defaults' => array('action' => 'vote_translation'),
            'before' => 'init_translation',
        ),                
        array(
            'regex' => '/(?P<action>\w+)\b', 
        ),        
    );    
    
    function init_translation()
    {
        $key = $this->param('key');
        $translation = $key->query_translations()->guid($this->param('translation_guid'))->get();
        if (!$translation)
        {
            return $this->not_found();
        }
        $this->params['translation'] = $translation;
    }
    
    function before()
    {
        $this->page_draw_vars['theme_name'] = 'simple_wide';
        $this->page_draw_vars['login_url'] = url_with_param($this->request->full_original_url(), 'login', 1);
    }    
    
    function action_index()
    {
        $key = $this->param('key');
        $group = $this->param('group');
        $language = $this->param('language');
        
        $key->init_defined_translation(true);
        
        return $this->page_draw(array(
            'title' => __('itrans:translations'),
            'header' => view('translate/header', array('items' => array($language, $group, $key))),
            'content' => view('translate/interface_key', array('key' => $key))
        ));       
    }        
    
    function action_add()
    {
        $action = new Action_AddInterfaceTranslation($this);
        $action->execute();
    }
    
    function action_vote_translation()
    {
        $action = new Action_VoteInterfaceTranslation($this);
        $action->execute();
    }    
    
    function action_delete_translation()
    {
        $action = new Action_DeleteInterfaceTranslation($this);
        $action->execute();        
    }    
    
    private function get_key_index($keys, $key)
    {
        $count = sizeof($keys);
        for ($i = 0; $i < $count; $i++)
        {
            $k = $keys[$i];
            if ($k->guid == $key->guid)
            {
                return $i;
            }
        }    
        return -1;
    }
    
    function action_prev()
    {
        $this->redirect_delta(-1);
    }

    function action_next()
    {
        $this->redirect_delta(1);
    }
    
    function redirect_delta($delta)
    {
        $group = $this->param('group');
        $key = $this->param('key');
            
        $keys = $group->get_available_keys();
        $filtered_keys = $this->parent_controller->filter_keys($keys);
                
        $i = $this->get_key_index($filtered_keys, $key);
        if ($i < 0)
        {
            forward($group->get_url());
        }
        
        $count = sizeof($filtered_keys);
        $next_key = $filtered_keys[($i + $delta + $count) % $count];
        forward($next_key->get_url());
    }
}