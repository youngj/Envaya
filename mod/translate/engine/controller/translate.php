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
            'action' => 'action_index',
        ),
        array(
            'regex' => '/(?P<action>instructions|delete_comment|check_translation)\b', 
        ),                
        array(
            'regex' => '/admin\b', 
            'controller' => 'Controller_TranslateAdmin',
        ),        
        array(
            'regex' => '/page/(?P<b64_keys>\w+)(/)?$', 
            'action' => 'action_page',
            'before' => 'init_language_keys',
        ),      
        array(
            'regex' => '/page/(?P<b64_keys>\w+)/(?P<key_name>\w+)', 
            'controller' => 'Controller_TranslatePageKey',
            'before' => 'init_language_keys_key',
        ),                
        array(
            'regex' => '/(?P<lang>\w+)/translators/(?P<guid>\d+)\b', 
            'action' => 'action_translator',
            'before' => 'init_language',
        ),        
        array(
            'regex' => '/(?P<lang>\w+)/translators(/)?$', 
            'action' => 'action_translators',
            'before' => 'init_language',
        ),
        array(
            'regex' => '/(?P<lang>\w+)/?$', 
            'action' => 'action_view_language',
            'before' => 'init_language',
        ),    
        array(
            'regex' => '/(?P<lang>\w+)/(?P<action>interface|content)(,(?P<filter>[\w\=\,]+))?(/)?$', 
            'before' => 'init_language',
        ),            
        array(
            'regex' => '/(?P<lang>\w+)/(?P<action>\w+)$', 
            'before' => 'init_language',
        ),
        array(
            'regex' => '/(?P<lang>\w+)/module/(?P<group_name>\w+)(,(?P<filter>[\w\=\,]+))?(/)?$', 
            'action' => 'action_view_group',
            'before' => 'init_language_group',
        ),
        array(
            'regex' => '/(?P<lang>\w+)/content(,(?P<filter>[\w\=\,]+))?/(?P<key_name>\w+)', 
            'controller' => 'Controller_TranslateEntityKey',
            'before' => 'init_language_key',
        ),        
        array(
            'regex' => '/(?P<lang>\w+)/interface(,(?P<filter>[\w\=\,]+))?/(?P<key_name>\w+)', 
            'controller' => 'Controller_TranslateInterfaceKey',
            'before' => 'init_language_key',
        ),                
        array(
            'regex' => '/(?P<lang>\w+)/module/(?P<group_name>\w+)(,(?P<filter>[\w\,\=]+))?/(?P<key_name>\w+)', 
            'controller' => 'Controller_TranslateGroupKey',
            'before' => 'init_language_group_key',
        ),
    );    
    
    function before()
    {
        $this->page_draw_vars['theme_name'] = 'simple_wide';
        $this->page_draw_vars['login_url'] = url_with_param(Request::full_original_url(), 'login', 1);
        $this->page_draw_vars['show_translate_footer'] = false;
    }

    function init_language()
    {
        $code = $this->param('lang');
        $language = TranslationLanguage::query()->where('code = ?', $code)->get();
        if (!$language)
        {
            throw new NotFoundException();
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
            throw new NotFoundException();
        }
        $this->params['group'] = $group;
    }
    
    function init_language_key()
    {    
        $this->init_language();
        
        $language = $this->param('language');
        $key_name = urldecode_alpha($this->param('key_name'));
        
        $key = $language->query_keys()->where('name = ?', $key_name)->get();

        if (!$key)
        {
            throw new NotFoundException();
        }
        
        Permission_ViewTranslation::require_for_entity($key);
        
        $this->params['key'] = $key;        
    }
    
    function init_language_group_key()
    {
        $this->init_language_group();
        $key_name = urldecode_alpha($this->param('key_name'));
        $group = $this->param('group');
        
        $key = $group->get_key_by_name($key_name);
        if (!$key)
        {
            throw new NotFoundException();
        }
        $this->params['key'] = $key;
    }
    
    function action_index()
    {
        Permission_Public::require_any();
    
        return $this->page_draw(array(
            'title' => __('itrans:translations'),
            'header' => view('translate/header'),
            'content' => view('translate/languages')
        ));
    }
     
    function action_instructions()
    {
        Permission_Public::require_any();
    
        return $this->page_draw(array(
            'title' => __('itrans:instructions'),
            'header' => view('translate/header', array('title' => __('itrans:instructions'))),
            'content' => view('translate/instructions')
        ));
    }
     

    
    function action_interface()
    {
        Permission_Public::require_any();
    
        $language = $this->param('language');      
        
        $filter = $this->get_filter_params();
        $filter_str = $this->get_filter_str($filter);        
        
        $query = InterfaceKey::query()
            ->where('language_guid = ?', $language->guid)
            ->order_by('time_updated desc, guid desc');

        $query = $this->filter_query($query, $filter);
            
        $base_url = "/tr/{$language->code}/interface" . ($filter_str ? ",$filter_str" : '');
        
        return $this->page_draw(array(
            'title' => __('itrans:interface_translations'),
            'header' => view('translate/header',  array('items' => array($language), 'title' => __('itrans:interface_translations'))),
            'content' => view('translate/interface_keys', array(
                'language' => $language,
                'query' => $query,
                'base_url' => $base_url,
                'filter' => $filter
            ))
        ));
    }     
     
    function action_content()
    {
        Permission_Public::require_any();
    
        $language = $this->param('language');
        
        $filter = $this->get_filter_params();
        $filter_str = $this->get_filter_str($filter);
        $base_url = "/tr/{$language->code}/content" . ($filter_str ? ",$filter_str" : '');        
        
        $query = EntityTranslationKey::query()
            ->where('language_guid = ?', $language->guid)
            ->order_by('time_updated desc, guid desc');
            
        $query = $this->filter_query($query, $filter);
        
        return $this->page_draw(array(
            'title' => __('itrans:translators'),
            'header' => view('translate/header', array('items' => array($language), 'title' => __('itrans:user_content'))),
            'content' => view('translate/user_content', array(
                'language' => $language,
                'base_url' => $base_url,
                'query' => $query,
                'filter' => $filter
            ))
        ));            
    }
     
    function action_view_language()
    {
        Permission_Public::require_any();
    
        $language = $this->param('language');
        
        return $this->page_draw(array(
            'title' => __('itrans:translations'),
            'header' => view('translate/header', array('items' => array($language))),
            'content' => view('translate/language', array('language' => $language))
        ));
    }           
    
    function action_translators()
    {
        Permission_Public::require_any();
    
        $language = $this->param('language');
    
        return $this->page_draw(array(
            'title' => __('itrans:translators'),
            'header' => view('translate/header', array('items' => array($language), 'title' => __('itrans:translators'))),
            'content' => view('translate/translators', array('language' => $language))
        ));        
    }
    
    function action_translator()
    {
        Permission_Public::require_any();
    
        $language = $this->param('language');
    
        $user = User::get_by_guid($this->param('guid'));
        if (!$user)
        {
            throw new NotFoundException();
        }
        $stats = $language->get_stats_for_user($user);
        if (!$stats->guid)
        {
            throw new NotFoundException();
        }
    
        return $this->page_draw(array(
            'title' => __('itrans:translator'),
            'header' => view('translate/header', array('items' => array(
                $language, 
                array('title' => __('itrans:translators'), 'url' => "{$language->get_url()}/translators"), 
                array('title' => $stats->get_display_name())
            ))),
            'content' => view('translate/translator', array('language' => $language, 'user' => $user, 'stats' => $stats))
        ));        
    }
    
    function action_latest()
    {
        Permission_Public::require_any();
    
        $language = $this->param('language');
        
        $filter = $this->get_filter_params();
        $filter_str = $this->get_filter_str($filter);
        $base_url = "/tr/{$language->code}/latest" . ($filter_str ? ",$filter_str" : '');              
        
        return $this->page_draw(array(
            'title' => __('itrans:latest'),
            'header' => view('translate/header', array('items' => array($language), 'title' => __('itrans:latest'))),
            'content' => view('translate/latest_translations', array(
                'language' => $language,
                'filter' => $filter,
                'base_url' => $base_url,
            ))
        ));    
    }
    
    function get_filter_params()
    {
        $query = get_input('q');
        $status = get_input('status');
        $filter_params = array();
        
        if (!$query && !$status)
        {            
            $filter = $this->param('filter');
            if ($filter)
            {                                            
                $filter_groups = explode(',', $filter);
                foreach ($filter_groups as $filter_group)
                {                    
                    $filter_group_arr = explode('=', $filter_group, 2); 
                    $filter_params[urldecode_alpha($filter_group_arr[0])] = urldecode_alpha($filter_group_arr[1]);
                }

                return $filter_params;
            }
        }

        if ($query)
        {
            $filter_params['q'] = $query;
        }
        if ($status)
        {
            $filter_params['status'] = $status;
        }
        return $filter_params;
    }
    
    function filter_query($query, $filter)
    {
        $status = @$filter['status'];
        $q = @$filter['q'];
        
        if ($q)
        {
            $query->where('best_translation like ? or name like ?', "%$q%", "%$q%");
        }   
        
        switch ($status)
        {
            case 'empty':
                return $query->where("best_translation = ''");
            case 'translated':
                return $query->where("best_translation <> ''");
            case 'unapproved':
                return $query->where("best_translation <> '' and best_translation_approval <= 0");
            case 'approved':
                return $query->where("best_translation_approval > 0");
        }
        return $query;
    
    }
    
    function filter_keys($keys, $filter)
    {
        $status = @$filter['status'];
        $query = @$filter['q'];
    
        $filtered_keys = array();
        
        foreach ($keys as $key)
        {
            $empty = ($key->best_translation == '');
            $approved = $key->best_translation_approval > 0;
               
            if ($status == 'empty' && !$empty
                || $status == 'translated' && $empty
                || $status == 'unapproved' && $approved
                || $status == 'approved' && !$approved)
            {
                continue;
            }
        
            if ($query)
            {
                $lq = strtolower($query);
                if (strpos($key->name, $lq) === false
                    && strpos(strtolower(__($key->name)), $lq) === false
                    && strpos(strtolower($key->best_translation), $lq) === false)
                {
                    continue;
                }
            }
            
            $filtered_keys[] = $key;
        }
        return $filtered_keys;        
    }
    
    function get_filter_str($filter)
    {
        $filter_parts = array();
        foreach ($filter as $k => $v)
        {
            $filter_parts[] = urlencode_alpha($k).'='.urlencode_alpha($v);
        }
        return implode(',', $filter_parts);       
    }

    function action_view_group()
    {
        Permission_Public::require_any();
        PageContext::add_header_html('<meta name="robots" content="noindex,nofollow" />');
    
        $group = $this->param('group');
        $language = $this->param('language');      

        $filter = $this->get_filter_params();
            
        $keys = $group->get_available_keys();
        $filtered_keys = $this->filter_keys($keys, $filter);
        
        $filter_str = $this->get_filter_str($filter);
        $base_url = "/tr/{$language->code}/module/{$group->name}" . ($filter_str ? ",$filter_str" : '');
        
        return $this->page_draw(array(
            'title' => __('itrans:translations'),
            'header' => view('translate/header',  array('items' => array($language, $group))),
            'content' => view('translate/interface_group', array(
                'group' => $group,
                'filter' => $filter,
                'all_keys' => $keys,
                'filtered_keys' => $filtered_keys,
                'base_url' => $base_url,
            ))
        ));       
    }    
    
    function action_delete_comment()
    {
        $this->validate_security_token();    
        
        $guid = (int)get_input('comment');
        $comment = TranslationKeyComment::get_by_guid($guid);
        
        if (!$comment)
        {
            throw new NotFoundException();
        }
        
        Permission_EditTranslation::require_for_entity($comment);
        
        $comment->disable();
        $comment->save();

        SessionMessages::add(__('comment:deleted'));            
        $this->redirect();
    }    
    
    function action_comments()
    {
        Permission_Public::require_any();
    
        $language = $this->param('language');
        
        return $this->page_draw(array(
            'title' => __('itrans:latest_comments'),
            'header' => view('translate/header', array('items' => array($language), 'title' => __('itrans:latest_comments'))),
            'content' => view('translate/latest_comments', array('language' => $language))
        ));            
    }
    
    function init_language_keys()
    {
        $this->params['lang'] = Language::get_current_code();
        $this->init_language();    
        $this->init_keys();
    }

    function init_language_keys_key()
    {
        $this->params['lang'] = Language::get_current_code();
        $this->init_language_key();
        $this->init_keys();
    }    

    function init_keys()
    {
        $b64_keys = urldecode_alpha($this->param('b64_keys'));
        
        try
        {
            $gz_keys = base64_decode($b64_keys);
            $keys_str = gzuncompress($gz_keys);
        }
        catch (ErrorException $ex)
        {
            throw new NotFoundException();
        }
        
        $uri_keys = explode(' ', $keys_str, 2);
        $this->params['page_uri'] = $uri_keys[0];
        
        $key_names = explode(",", $uri_keys[1]);
        
        $language = $this->param('language');
        
        $this->params['keys'] = $language->query_keys()
            ->where_in('name', $key_names)
            ->order_by('subtype_id, name')
            ->filter();
            
        if (!sizeof($this->params['keys']))
        {
            throw new NotFoundException();
        }
    }
    
    function action_page()
    {
        Permission_Public::require_any();
        PageContext::add_header_html('<meta name="robots" content="noindex,nofollow" />');
    
        $language = $this->param('language');
        $keys = $this->param('keys');
        $page_uri = $this->param('page_uri');
        
        return $this->page_draw(array(
            'title' => __('itrans:edit_page'),
            'header' => view('translate/page_header', array(
                'items' => array(
                    array('url' => $page_uri, 'title' => $page_uri),
                    array('title' => $language->name),
                ),
            )),            
            'content' => view('translate/page', array(
                'language' => $language, 
                'base_url' => Request::get_uri(),
                'keys' => $keys
            ))
        ));
    }
    
    function action_check_translation()
    {
        Permission_Public::require_any();
    
        $this->set_content_type('text/javascript');
    
        $source = (int)get_input('source');
    
        $keys = explode(',', get_input('keys'));
        
        $query = Translation::query()->where_in('container_guid', $keys);
        
        if ($source)
        {
            $query->where('source = ?', $source);
        }           
        
        $has_current_translation = false;
        
        foreach ($query->filter() as $translation)
        {
            if (!$translation->is_stale())
            {
                $has_current_translation = true;
                break;
            }
        }
            
        $this->set_content(json_encode(array('has_translation' => $has_current_translation)));
    }
}