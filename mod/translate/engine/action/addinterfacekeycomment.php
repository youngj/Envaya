<?php

class Action_AddInterfaceKeyComment extends Action
{
    function process_input()
    {
        $this->require_login();
        
        $content = get_input('content');
        
        if ($content == '')
        {
            throw new ValidationException(__('comment:empty'));
        }
                
        $key = $this->param('key');
        if (!$key->guid)
        {
            $key->save();
        }
        
        if ($key->query_comments()->where('content = ?', $content)->exists())
        {
            throw new ValidationException(__('comment:duplicate'));
        }
        
        $user = Session::get_loggedin_user();
                
        $comment = new InterfaceKeyComment();
        $comment->container_guid = $key->guid;
        $comment->owner_guid = $user->guid;
        if (get_input('scope') == 'current')
        {
            $comment->language_guid = $key->language_guid;
        }
        $comment->key_name = $key->name;
        $comment->set_content($content);
		$comment->save();

        $key->update();
        $key->get_container_entity()->update();
        
        SessionMessages::add(__('comment:success'));
        redirect_back();
    }
}