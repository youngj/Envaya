<?php    

class Action_Registration_CreateProfileBase extends Action
{
    protected function post_process_input() {} // subclasses should override

    function process_input()
    {
        $this->require_login();
    
        $org = Session::get_logged_in_user();

        $mission = get_input('mission');
        if (!$mission)
        {
            throw new ValidationException(__("register:mission:blank"));
        }

        $sectors = get_input_array('sector');
        if (sizeof($sectors) == 0)
        {
            throw new ValidationException(__("register:sector:blank"));
        }
        else if (sizeof($sectors) > 5)
        {
            throw new ValidationException(__("register:sector:toomany"));
        }

        $org->language = Language::get_current_code();
        $org->set_sectors($sectors);
        $org->city = get_input('city');
        $org->region = get_input('region');
        $org->set_metadata('sector_other', get_input('sector_other'));

        $org->set_design_setting('theme_name', get_input('theme'));
        $org->set_design_setting('tagline', $org->get_location_text(false));
        
        $org->geocode_lat_long();

        $home = $org->get_widget_by_class('Home');
        if (!$home->guid)
        {
            $home->save();
        }
        $mission_section = $home->get_widget_by_class('Mission');        
        $mission_section->set_content($mission);                
        $mission_section->save();

        $prevSetupState = $org->setup_state;
        
        $org->setup_state = Organization::CreatedHomePage;
        $org->save();

        $org->update_scope();
        $org->save();
        
        if ($prevSetupState < $org->setup_state && !$org->is_approved())
        {
            FeedItem_Register::post($org, $org);

            OutgoingMail::create(
                sprintf(__('email:registernotify:subject'), $org->name), 
                sprintf(__('email:registernotify:body'), $org->get_url())
            )->send_to_admin();
        }            
        
        SessionMessages::add(__("register:homepage_created"));
        
        $this->post_process_input();
    }    
}