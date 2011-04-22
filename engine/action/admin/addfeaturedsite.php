<?php

class Action_Admin_AddFeaturedSite extends Action
{
    protected $user;

    function before()
    {
        $this->require_admin();
        
        $username = get_input('username');
        $user = User::get_by_username($username);
        if (!$user)
        {
            return $this->not_found();
        }   

        $this->user = $user;
    }
     
    function process_input()
    {
        $user = $this->user;
        $featuredSite = new FeaturedSite();
        $featuredSite->container_guid = $user->guid;
        $featuredSite->image_url = get_input('image_url');
        $featuredSite->set_content(get_input('content'));
        $featuredSite->save();
        SessionMessages::add('featured:created');
        forward('org/featured');
    }

    function render()
    {
        $this->page_draw(array(
            'title' => __('featured:add'),
            'content' => view('admin/add_featured', array('entity' => $this->user)),
        ));                
    }    
}    