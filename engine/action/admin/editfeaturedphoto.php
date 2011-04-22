<?php

class Action_Admin_EditFeaturedPhoto extends Action
{
    function before()
    {
        $this->require_admin();
    }
     
    function process_input()
    {
        $photo = FeaturedPhoto::get_by_guid(get_input('guid'));
        if (!$photo)
        {
            return $this->not_found();
        }        
        
        if (get_input('delete'))
        {
            $photo->delete();
            
            SessionMessages::add(__("featured_photo:deleted"));
            return forward("/admin/featured_photos");        
        }        
                                
        $photo->x_offset = (int)get_input('x_offset');
        $photo->y_offset = (int)get_input('y_offset');
        $photo->weight = (double)get_input('weight');
        $photo->image_url = get_input('image_url');        
        $photo->href = get_input('href');
        $photo->caption = get_input('caption');
        $photo->org_name = get_input('org_name');
        $photo->active = get_input('active') == 'yes' ? 1 : 0;
        $photo->save();
        
        SessionMessages::add(__("featured_photo:saved"));
        forward("/admin/featured_photos");    
    }

    function render()
    {
        $photo = FeaturedPhoto::get_by_guid(get_input('guid'));        
        if (!$photo)
        {
            return $this->not_found();
        }
        
        $this->page_draw(array(
            'title' => __('featured_photo:edit'),
            'content' => view('admin/edit_featured_photo', array(
                'photo' => $photo,
            ))
        ));      
    }    
}    