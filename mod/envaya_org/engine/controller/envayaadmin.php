<?php

class Controller_EnvayaAdmin extends Controller
{
    static $routes; // initialized at bottom of file

    function before()
    {
        Permission_EditMainSite::require_for_root();
        $this->page_draw_vars['theme_name'] = 'editor';
    }

    function action_activate_featured()
    {
        $action = new Action_Admin_ActivateFeaturedSite($this);
        $action->execute();
    }
    
    function action_add_featured()
    {
        $action = new Action_Admin_AddFeaturedSite($this);
        $action->execute();
    }       
        
    function action_edit_featured()
    {
        $action = new Action_Admin_EditFeaturedSite($this);
        $action->execute();    
    }

    function action_add_featured_photo()
    {
        $action = new Action_Admin_AddFeaturedPhoto($this);
        $action->execute();
    }
    
    function action_edit_featured_photo()
    {
        $action = new Action_Admin_EditFeaturedPhoto($this);
        $action->execute();  
    }   
    
    function action_featured_photos()
    {
        $this->page_draw(array(
            'title' => __('featured:photo:all'),
            'content' =>  view('admin/featured_photos', array(
                'photos' => FeaturedPhoto::query()->filter()
            )),
            'theme_name' => 'editor'
        ));
    }
    
    function action_home_page()
    {
        $action = new Action_Admin_ConfigureHomePage($this);
        $action->execute();  
    }
}

Controller_EnvayaAdmin::$routes = Controller::$SIMPLE_ROUTES;