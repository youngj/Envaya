<?php

/*
 * Controller for allowing users to contribute translations.
 *
 * URL: /tr/...
 */
class Controller_Translate extends Controller
{
    static $routes = array(
        array(
            'regex' => '(/)?$', 
            'defaults' => array('action' => 'index'), 
        ),
        array(
            'regex' => '/admin\b', 
            'defaults' => array('controller' => 'TranslateAdmin'), 
        ),        
        array(
            'regex' => '/(?P<lang>\w+)(/)?$', 
            'defaults' => array('action' => 'view_language'), 
            'before' => 'init_language',
        ),
        array(
            'regex' => '/(?P<lang>\w+)/(?P<group_name>\w+)(/)?$', 
            'defaults' => array('action' => 'view_group'), 
            'before' => 'init_language_group',
        ),
        array(
            'regex' => '/(?P<lang>\w+)/(?P<group_name>\w+)/(?P<key_name>[\w\%\:]+)', 
            'defaults' => array('controller' => 'TranslateKey'), 
            'before' => 'init_language_group_key',
        ),
    );
    
    function before()
    {
        $this->page_draw_vars['theme_name'] = 'simple_wide';
    }

    function init_language()
    {
        $code = $this->param('lang');
        $language = InterfaceLanguage::query()->where('code = ?', $code)->get();
        if (!$language)
        {
            return $this->not_found();
        }
        $this->params['language'] = $language;
    }
        
    function init_language_group()
    {
        $this->init_language();
        $language = $this->param('language');
        $group_name = $this->param('group_name');
        
        $group = $language->query_groups()->where('name = ?', $group_name)->get();
        if (!$group)
        {
            return $this->not_found();
        }
        $this->params['group'] = $group;
    }
    
    function init_language_group_key()
    {
        $this->init_language_group();
        $key_name = $this->param('key_name');
        $group = $this->param('group');
        
        $key = $group->get_key_by_name($key_name);
        $this->params['key'] = $key;
    }
    
    function action_index()
    {
        return $this->page_draw(array(
            'title' => __('itrans:title'),
            'header' => view('translate/header'),
            'content' => view('translate/interface_languages')
        ));
    }
    
    function action_view_language()
    {
        $language = $this->param('language');
        
        return $this->page_draw(array(
            'title' => __('itrans:title'),
            'header' => view('translate/header', array('items' => array($language))),
            'content' => view('translate/interface_language', array('language' => $language))
        ));
    }

    function action_view_group()
    {
        $group = $this->param('group');
        $language = $this->param('language');
        
        return $this->page_draw(array(
            'title' => __('itrans:title'),
            'header' => view('translate/header',  array('items' => array($language, $group))),
            'content' => view('translate/interface_group', array('group' => $group))
        ));       
    }    
}