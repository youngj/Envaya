<?php

class Action_Admin_EditFeaturedSite extends Action
{
    protected $featuredSite;

    function before()
    {
        $this->require_admin();

        $guid = get_input('guid');
        $featuredSite = FeaturedSite::get_by_guid($guid);
        if (!$featuredSite)
        {
            return $this->not_found();
        }
        $this->featuredSite = $featuredSite;        
    }
     
    function process_input()
    {
        $featuredSite = $this->featuredSite;
        $featuredSite->image_url = get_input('image_url');
        $featuredSite->set_content(get_input('content'));
        $featuredSite->save();
        SessionMessages::add('featured:saved');
        forward('org/featured');
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