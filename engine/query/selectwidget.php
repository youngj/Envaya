<?php

class Query_SelectWidget extends Query_SelectEntity
{
    function where_published($published = true)
    {    
        if ($published)
        {
            return $this->where('publish_status = ?', Widget::Published);
        }
        else
        {
            return $this->where('publish_status <> ?', Widget::Published);
        }        
    }
    
    private $joined_users = false;
    
    function join_users()
    {
        if (!$this->joined_users)
        {
            $alias = $this->get_alias();
            $this->join("INNER JOIN users u ON u.guid = $alias.user_guid");
            $this->joined_users = true;
        }
    }
    
    function where_visible_to_user()
    {        
        if (!Permission_ViewUserSite::has_for_root())
        {
            $this->join_users();
            $user = Session::get_logged_in_user();
            if ($user)
            {
                $this->where("(u.approval > 0 OR user_guid = ?)", $user->guid);
            }
            else
            {
                $this->where('u.approval > 0');
            }
        }
        return $this;
    }    
    
    function where_in_menu($in_menu = true)
    {
        return $this->where_published()->where('in_menu = 1');
    }
}