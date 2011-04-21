<?php    

class Action_Registration_CreateProfileBase extends Action
{
    protected function post_process_input() {} // subclasses should override

    function process_input()
    {
        $this->require_login();
    
        $org = Session::get_loggedin_user();

        $mission = get_input('mission');
        if (!$mission)
        {
            throw new ValidationException(__("setup:mission:blank"));
        }

        $sectors = get_input_array('sector');
        if (sizeof($sectors) == 0)
        {
            throw new ValidationException(__("setup:sector:blank"));
        }
        else if (sizeof($sectors) > 5)
        {
            throw new ValidationException(__("setup:sector:toomany"));
        }

        $org->language = get_input('content_language');
        $org->set_sectors($sectors);
        $org->city = get_input('city');
        $org->region = get_input('region');
        $org->sector_other = get_input('sector_other');

        $org->theme = get_input('theme');

        $latlong = Geography::geocode($org->get_location_text());

        if ($latlong)
        {
            $org->set_lat_long($latlong['lat'], $latlong['long']);
        }

        $home = $org->get_widget_by_class('Home');
        if (!$home->guid)
        {
            $home->save();
        }
        $mission_section = $home->get_widget_by_class('Mission');        
        $mission_section->set_content($mission);                
        $mission_section->save();

        $prevSetupState = $org->setup_state;
        
        $org->setup_state = SetupState::CreatedHomePage;
        $org->save();

        if ($prevSetupState < $org->setup_state && !$org->is_approved())
        {
            FeedItem_Register::post($org, $org);

            OutgoingMail::create(
                sprintf(__('email:registernotify:subject'), $org->name), 
                sprintf(__('email:registernotify:body'), $org->get_url().'?login=1')
            )->send_to_admin();
        }            
        
        SessionMessages::add(__("setup:ok"));
        
        $this->post_process_input();
    }    
}