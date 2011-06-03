<?php

class Action_Admin_EditFeaturedPhoto extends Action
{
    protected $photo;

    function before()
    {
        $this->require_admin();

        $photo = FeaturedPhoto::get_by_guid(get_input('guid'));        
        if (!$photo)
        {
            throw new NotFoundException();
        }
        $this->photo = $photo;        
    }
     
    function process_input()
    {
        $photo = $this->photo;
        
        if (get_input('delete'))
        {
            $photo->delete();
            
            SessionMessages::add(__("featured:photo:deleted"));
            $this->redirect("/admin/envaya/featured_photos");        
        }   
        else
        {                               
            $photo->x_offset = (int)get_input('x_offset');
            $photo->y_offset = (int)get_input('y_offset');
            $photo->weight = (double)get_input('weight');
            $photo->image_url = get_input('image_url');        
            $photo->href = get_input('href');
            $photo->caption = get_input('caption');
            $photo->org_name = get_input('org_name');
            $photo->active = get_input('active') == 'yes' ? 1 : 0;
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