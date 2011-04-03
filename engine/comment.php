<?php

/*
 * Represents a comment about a news update (or other entity).
 * Each news update may have multiple associated comments.
 * Comments may be associated with a registered User, or anonymous.
 */
class Comment extends Entity
{
    static $table_name = 'comments';
    static $table_attributes = array(
        'content' => '',
        'data_types' => 0,
        'language' => '',
        'name' => '',
        'location' => '',
    );   
    
	function get_name()
	{
		$owner = $this->get_owner_entity();
		if ($owner)
		{
			return $owner->name;
		}
		else
		{
			return $this->name ?: __('comment:anonymous');
		}
	}
	
    function can_user_edit($user)
    {           
        if (parent::can_user_edit($user))
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
