<?php

class Action_Admin_AddFeaturedPhoto extends Action
{
    function before()
    {
        Permission_EditMainSite::require_for_root();
    }
     
    function process_input()
    {
        $photo = new FeaturedPhoto();
        $photo->user_guid = Input::get_string('user_guid');
        $photo->image_url = Input::get_string('image_url');
        $photo->x_offset = Input::get_int('x_offset');
        $photo->y_offset = Input::get_int('y_offset');
        $photo->weight = (double)Input::get_string('weight');
        $photo->href = Input::get_string('href');
        $photo->caption = Input::get_string('caption');
        $photo->org_name = Input::get_string('org_name');
        $photo->active = Input::get_string('active') == 'yes' ? 1 : 0;
        $photo->save();
        
        SessionMessages::add(__("featured:photo:added"));
        $this->redirect("/admin/envaya/featured_photos");
    }

    function render()
    {
        $this->page_draw(array(
            'title' => __('featured:photo:add'),
            'content' => view('admin/add_featured_photo', array(
                'image_url' => Input::get_string('image_url'),
                'href' => Input::get_string('href'),
                'user_guid' => Input::get_string('user_guid')
            )),
        ));
    }    
}    