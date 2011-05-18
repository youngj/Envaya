<?php

class Action_Admin_AddFeaturedPhoto extends Action
{
    function before()
    {
        $this->require_admin();
    }
     
    function process_input()
    {
        $photo = new FeaturedPhoto();
        $photo->user_guid = get_input('user_guid');
        $photo->image_url = get_input('image_url');
        $photo->x_offset = (int)get_input('x_offset');
        $photo->y_offset = (int)get_input('y_offset');
        $photo->weight = (double)get_input('weight');
        $photo->href = get_input('href');
        $photo->caption = get_input('caption');
        $photo->org_name = get_input('org_name');
        $photo->active = get_input('active') == 'yes' ? 1 : 0;
        $photo->save();
        
        SessionMessages::add(__("featured_photo:added"));
        $this->redirect("/admin/featured_photos");
    }

    function render()
    {
        $this->page_draw(array(
            'title' => __('featured_photo:add'),
            'content' => view('admin/add_featured_photo', array(
                'image_url' => get_input('image_url'),
                'href' => get_input('href'),
                'user_guid' => get_input('user_guid')
            )),
        ));
    }    
}    