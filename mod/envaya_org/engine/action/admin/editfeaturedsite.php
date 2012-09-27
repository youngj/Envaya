<?php

class Action_Admin_EditFeaturedSite extends Action
{
    protected $featuredSite;

    function before()
    {
        Permission_EditMainSite::require_for_root();

        $guid = Input::get_string('guid');
        $featuredSite = FeaturedSite::get_by_guid($guid);
        if (!$featuredSite)
        {
            throw new NotFoundException();
        }
        $this->featuredSite = $featuredSite;        
    }
     
    function process_input()
    {
        $featuredSite = $this->featuredSite;
        $featuredSite->image_url = Input::get_string('image_url');
        $featuredSite->set_content(Input::get_string('content'));
        $featuredSite->save();
        SessionMessages::add('featured:saved');
        $this->redirect('/org/featured');
    }

    function render()
    {    
        $featuredSite = $this->featuredSite;
        $this->page_draw(array(
            'title' => __('featured:edit'),
            'content' => view('admin/edit_featured', array('entity' => $featuredSite)),
        ));
    }    
}    