<?php

class Action_Admin_EditFeaturedPhoto extends Action
{
    protected $photo;

    function before()
    {
        Permission_EditMainSite::require_for_root();

        $photo = FeaturedPhoto::get_by_guid(Input::get_string('guid'));        
        if (!$photo)
        {
            throw new NotFoundException();
        }
        $this->photo = $photo;        
    }
     
    function process_input()
    {
        $photo = $this->photo;
        
        if (Input::get_string('delete'))
        {
            $photo->disable();
            $photo->save();
            
            SessionMessages::add(__("featured:photo:deleted"));
            $this->redirect("/admin/envaya/featured_photos");        
        }   
        else
        {                               
            $photo->x_offset = Input::get_int('x_offset');
            $photo->y_offset = Input::get_int('y_offset');
            $photo->weight = (double)Input::get_string('weight');
            $photo->image_url = Input::get_string('image_url');        
            $photo->href = Input::get_string('href');
            $photo->caption = Input::get_string('caption');
            $photo->org_name = Input::get_string('org_name');
            $photo->active = Input::get_string('active') == 'yes' ? 1 : 0;
            $photo->save();
            
            SessionMessages::add(__("featured:photo:saved"));
            $this->redirect("/admin/envaya/featured_photos");    
        }
    }

    function render()
    {
        $photo = $this->photo;
    
        $this->page_draw(array(
            'title' => __('featured:photo:edit'),
            'content' => view('admin/edit_featured_photo', array(
                'photo' => $photo,
            ))
        ));      
    }    
}    