<?php

class Controller_TranslateKey extends Controller
{
    static $routes = array(
        array(
            'regex' => '(/)?$', 
            'action' => 'action_index',
        ),
        array(
            'regex' => '/(?P<translation_guid>\d+)/(?P<action>\w+)\b', 
            'action' => 'action_translation_<action>',
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
            throw new NotFoundException();
        }
        $this->params['translation'] = $translation;
    }
    
    function before()
    {
        PageContext::add_header_html('<meta name="robots" content="noindex,nofollow" />');
    
        $this->page_draw_vars['theme_name'] = 'simple_wide';
        $this->page_draw_vars['login_url'] = url_with_param(Request::full_original_url(), 'login', 1);
    }    
    
    function action_index()
    {
        $key = $this->param('key');
        $translation_guid = (int)get_input('translation');
        if ($translation_guid)
        {
            $translation = $key->query_translations()->where('guid = ?', $translation_guid)->get();
        }
        else
        {
            $translation = null;
        }        
    
        return $this->index_page_draw(array(
            'title' => __('itrans:translations'),
            'content' => view('translate/key', array(
                'key' => $key, 
                'translation' => $translation
            ))
        ));
    }        
          
    function action_add()
    {
        $action = new Action_AddTranslation($this);
        $action->execute();
    }

    function action_add_comment()
    {
        $action = new Action_AddTranslationKeyComment($this);
        $action->execute();
    }

    function action_translation_set_approval()
    {
        $action = new Action_SetTranslationApproval($this);
        $action->execute();    
    }    
    
    function action_translation_vote()
    {
        $action = new Action_VoteTranslation($this);
        $action->execute();
    }

    function action_translation_delete()
    {
        $action = new Action_DeleteTranslation($this);
        $action->execute();
    }        
        
    private function get_key_index($keys, $key)
    {
        $count = sizeof($keys);
        for ($i = 0; $i < $count; $i++)
        {
            $k = $keys[$i];
            if ($k->name == $key->name)
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
    
    function get_available_keys()
    {
        throw new NotImplementedException();
    }
    
    function get_parent_uri()
    {
        $uri = $this->parent_controller->get_matched_uri();
        $pieces = explode('/', $uri);
        unset($pieces[sizeof($pieces)-1]);
        return implode('/', $pieces);
    }    
    
    function redirect_delta($delta)
    {
        $keys = $this->get_available_keys();
        $key = $this->param('key');
        
        $parent_uri = $this->get_parent_uri();
                
        $i = $this->get_key_index($keys, $key);
        
        if ($i >= 0)
        {
            $filter = $this->parent_controller->get_filter_params();
            $filtered_keys = $this->parent_controller->filter_keys($keys, $filter);            
            
            $num_keys = sizeof($keys);

            for ($j = $i + $delta; $j >= 0 && $j < $num_keys; $j += $delta)
            {
                $next_key = $keys[$j];
                
                if ($this->get_key_index($filtered_keys, $next_key) >= 0)
                {
                    return $this->redirect($parent_uri . "/" . urlencode_alpha($next_key->name));
                }
            }
        }                
        
        return $this->redirect($parent_uri);
    }    
    
    function action_base_lang()
    {
        $action = new Action_ChangeBaseLanguage($this);
        $action->execute();    
    }
}