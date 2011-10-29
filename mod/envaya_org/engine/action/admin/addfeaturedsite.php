<?php

class Action_Admin_AddFeaturedSite extends Action
{
    protected $user;

    function before()
    {
        Permission_EditMainSite::require_for_root();
        
        $username = get_input('username');
        $user = User::get_by_username($username);
        if (!$user)
        {
            throw new NotFoundException();
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
        $this->redirect('/org/featured');
    }

    function render()
    {
        $this->page_draw(array(
            'title' => __('featured:add'),
            'content' => view('admin/add_featured', array('entity' => $this->user)),
        ));                
    }    
}    