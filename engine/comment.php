<?php

class Comment extends Entity
{
    static $subtype_id = T_comment;
    static $table_name = 'comments';
    static $table_attributes = array(
        'content' => '',
        'data_types' => 0,
        'language' => '',
        'name' => '',
    );   
    
    function can_edit($user = null)
    {   
        if (!$user)
            $user = Session::get_loggedin_user();            
        
        if (parent::can_edit($user))
        {
            return true;
        }

        $posted_comments = Session::get('posted_comments');
        
        if (is_array($posted_comments) && in_array($this->guid,$posted_comments))
        {
            return true;
        }
        return false;
    }       
}
