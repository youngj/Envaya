<?php    

class Action_Registration_CreateProfileBase extends Action
{
    protected function post_process_input() {} // subclasses should override

    function process_input()
    {   
        $org = Session::get_logged_in_user();

        $mission = Input::get_string('mission');
        if (!$mission)
        {
            throw new ValidationException(__("register:mission:blank"));
        }

        $sectors = Input::get_array('sector');
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
        $org->set_metadata('sector_other', Input::get_string('sector_other'));
        
        $home = Widget_Home::get_or_init_for_entity($org);
        $home->set_content($mission);
        $home->save();

        $prevSetupState = $org->setup_state;
        
        $org->setup_state = User::SetupComplete;
        $org->save();

        $org->update_scope();
        $org->save();
        
        if ($prevSetupState < $org->setup_state && !$org->is_approved())
        {
            FeedItem_Register::post($org, $org);            
            EmailSubscription_Registration::send_notifications(Organization::Registered, $org);
        }            
        
        SessionMessages::add(__("register:homepage_created"));
        
        $this->post_process_input();
    }    
}