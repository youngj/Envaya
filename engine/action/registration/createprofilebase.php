<?php    

class Action_Registration_CreateProfileBase extends Action
{
    protected function _process_input()
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

        $homeWidget = $org->get_widget_by_name('home');
        $homeWidget->set_content($mission);

        $org->language = get_input('content_language');

        $org->set_sectors($sectors);
        $org->city = get_input('city');
        $org->region = get_input('region');
        $org->sector_other = get_input('sector_other');

        $org->theme = get_input('theme');

        $latlong = Geocoder::geocode($org->get_location_text());

        if ($latlong)
        {
            $org->set_lat_long($latlong['lat'], $latlong['long']);
        }

        $homeWidget->save();

        $prevSetupState = $org->setup_state;
        
        $org->setup_state = SetupState::CreatedHomePage;
        $org->save();

        if ($prevSetupState < $org->setup_state && !$org->is_approved())
        {
            post_feed_items($org, 'register', $org);

            send_admin_mail(Zend::mail(
                sprintf(__('email:registernotify:subject'), $org->name), 
                sprintf(__('email:registernotify:body'), $org->get_url().'?login=1')
            ));
        }            
        
        SessionMessages::add(__("setup:ok"));
    }    
}