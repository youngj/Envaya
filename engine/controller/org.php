<?php

class Controller_Org extends Controller
{
    function before()
    {
        $this->add_generic_footer();
    }

    function action_browse()
    {
        $title = __("browse:title");
        $sector = get_input('sector');

        if (get_input("list"))
        {
            $area = view("org/browseList", array('lat' => $lat, 'long' => $long, 'sector' => $sector));
        }
        else
        {
            $lat = get_input('lat');
            $long = get_input('long');
            $zoom = get_input('zoom');

            $area = view("org/browseMap", array('lat' => $lat, 'long' => $long, 'zoom' => $zoom, 'sector' => $sector));
        }

        $body = view_layout('one_column', view_title($title), $area);

        $this->page_draw($title, $body);
    }

    function action_search()
    {
        $query = get_input('q');

        if ($query)
        {
            $title = sprintf(__('search:title_with_query'),$query);
        }
        else
        {
            $title = __('search:title');
        }
        $content = view('org/search', array('query' => $query, 'sector' => get_input('sector')));

        $body = view_layout('one_column_padded', view_title(__('search:title')), $content);

        $this->page_draw($title,$body);
    }

    function action_feed()
    {
        $title = __("feed:title");

        $area = view("org/feed");

        PageContext::set_translatable(false);

        $body = view_layout('one_column', view_title($title), $area);

        $this->page_draw($title, $body);
    }

    function action_new()
    {
        $step = ((int) get_input('step')) ?: 1;

        if ($step > 3)
        {
            $step = 1;
        }

        $loggedInUser = Session::get_loggedin_user();

        if ($loggedInUser && !($loggedInUser instanceof Organization))
        {
            logout();
            forward("org/new");
        }

        if ($step == 3 && !$loggedInUser)
        {
            register_error(__("create:notloggedin"));
            $step = 1;
            forward('pg/login');
        }

        if ($loggedInUser  && $step < 3)
        {
            $step = 3;
        }

        if ($step == 2 && !Session::get('registration'))
        {
            register_error(__("qualify:missing"));
            $step = 1;
        }

        $title = __("register:title");
        $body = view_layout('one_column', view_title($title, array('org_only' => true)), view("org/register$step"));
        $this->page_draw($title, $body);
    }

    function action_register1()
    {
        $approvedCountries = array('tz');

        try
        {
            $country = get_input('country');

            if (!in_array($country, $approvedCountries))
            {
                throw new RegistrationException(__("qualify:wrong_country"));
            }

            $orgType = get_input('org_type');
            if ($orgType != 'np')
            {
                throw new RegistrationException(__("qualify:wrong_org_type"));
            }

            Session::set('registration', array(
                //'registration_number' => get_input('registration_number'),
                'country' => get_input('country'),
            ));

            system_message(__("qualify:ok"));
            forward("org/new?step=2");

        }
        catch (RegistrationException $r)
        {
            action_error($r->getMessage());
        }
    }

    function action_register2()
    {
        $this->validate_security_token();

        try
        {
            $name = trim(get_input('org_name'));

            if (!$name)
            {
                throw new RegistrationException(__('create:no_name'));
            }

            $username = trim(get_input('username'));

            validate_username($username);

            if (get_user_by_username($username))
            {
                throw new RegistrationException(__('create:username_exists'));
            }

            $password = get_input('password');
            $password2 = get_input('password2');

            if (strcmp($password, $password2) != 0)
            {
                throw new RegistrationException(__('create:passwords_differ'));
            }

            $lpassword = strtolower($password);
            $lusername = strtolower($username);
            $lname = strtolower($name);

            if (strpos($lname, $lpassword) !== FALSE || strpos($lusername, $lpassword) !== FALSE)
            {
                throw new RegistrationException(__('create:password_too_easy'));
            }

            validate_password($password);

            $email = trim(get_input('email'));

            validate_email_address($email);

            $org = new Organization();
            $org->username = $username;
            $org->phone_number = get_input('phone');
            $org->email = $email;
            $org->name = $name;
            $org->set_password($password);
            $org->owner_guid = 0;
            $org->container_guid = 0;
            $org->language = get_language();
            $org->theme = "green";
            $org->setup_state = 3;

            $prevInfo = Session::get('registration');

            //$org->registration_number = $prevInfo['registration_number'];
            $org->country = $prevInfo['country'];
            //$org->local = $prevInfo['local'];

            $org->set_lat_long(-6.140555,35.551758);

            $org->save();

            /* auto-create empty pages */
            $org->get_widget_by_name('news')->save();

            $contactWidget = $org->get_widget_by_name('contact');
            if ($email)
            {
                $contactWidget->public_email = "yes";
            }
            $contactWidget->save();

            $guid = $org->guid;

            login($org, false);

            Session::set('registration', null);

            system_message(__("create:ok"));

            forward("org/new?step=3");
        }
        catch (RegistrationException $r)
        {
            action_error($r->getMessage());
        }
    }

    function action_register3()
    {
        $this->require_login();
        $this->validate_security_token();

        try
        {
            $org = Session::get_loggedin_user();

            $mission = get_input('mission');
            if (!$mission)
            {
                throw new RegistrationException(__("setup:mission:blank"));
            }

            $sectors = get_input_array('sector');
            if (sizeof($sectors) == 0)
            {
                throw new RegistrationException(__("setup:sector:blank"));
            }
            else if (sizeof($sectors) > 5)
            {
                throw new RegistrationException(__("setup:sector:toomany"));
            }

            $homeWidget = $org->get_widget_by_name('home');
            $homeWidget->set_content($mission, true);

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
            
            $org->setup_state = 5;
            $org->save();

            if ($prevSetupState < $org->setup_state && !$org->is_approved())
            {
                post_feed_items($org, 'register', $org);

                send_admin_mail(sprintf(__('email:registernotify:subject'), $org->name), 
                    sprintf(__('email:registernotify:body'), $org->get_url().'?login=1')
                );
            }            
            
            system_message(__("setup:ok"));
            

            forward($org->get_url());
        }
        catch (RegistrationException $r)
        {
            action_error($r->getMessage());
        }
    }

    function action_searchArea()
    {
        $latMin = get_input('latMin');
        $latMax = get_input('latMax');
        $longMin = get_input('longMin');
        $longMax = get_input('longMax');
        $sector = get_input('sector');

        $orgs = Organization::query_by_area(array($latMin, $longMin, $latMax, $longMax), $sector)->filter();

        $orgJs = array();

        foreach ($orgs as $org)
        {
            $orgJs[] = $org->js_properties();
        }

        $this->request->headers['Content-Type'] = 'text/javascript';
        $this->request->response = json_encode($orgJs);
    }

    function action_emailSettings()
    {
        $email = get_input('e');
        $code = get_input('c');
        $users = User::query(true)->where('email = ?', $email)->filter();

        $title = __("user:notification:label");

        if ($email && $code == get_email_fingerprint($email) && sizeof($users) > 0)
        {
            $area1 = view('org/emailSettings', array('email' => $email, 'users' => $users));
        }
        else
        {
            $area1 = __("user:notification:invalid");
        }
        $body = view_layout("one_column_padded", view_title($title), $area1);

        $this->page_draw($title, $body);
    }

    function action_emailSettings_save()
    {
        $email = get_input('email');
        $code = get_input('code');
        $enable_batch_email = get_input('enable_batch_email');
        $users = User::query(true)->where('email = ?', $email)->filter();

        foreach ($users as $user)
        {
            $user->enable_batch_email = $enable_batch_email;
            $user->save();

            system_message(__('user:notification:success'));
        }

        forward("/");
    }

    function action_selectImage()
    {
        $this->page_draw_vars['no_top_bar'] = true;

        $file = get_file_from_url(get_input('src'));

        $content = view('org/selectImage',
            array(
                'current' => $file,
                'position' => get_input('pos'),
                'frameId' => get_input('frameId'),
            )
        );
        $this->page_draw('',$content);
    }

    function action_translate()
    {
        $this->require_admin();
        PageContext::set_theme('editor');

        $props = get_input_array("prop");
        $from = get_input('from');
        
        $targetLang = get_input('targetlang') ?: get_language();

        $area2 = array();

        foreach ($props as $propStr)
        {
            $guidProp = explode('.', $propStr);
            $guid = $guidProp[0];
            $prop = $guidProp[1];
            $isHTML = (int)$guidProp[2];

            $entity = get_entity($guid);

            if ($entity && $entity->can_edit() && $entity->get($prop))
            {
                $area2[] = view("translation/translate",
                    array(
                        'entity' => $entity,
                        'property' => $prop,
                        'targetLang' => $targetLang,
                        'isHTML' => $isHTML,
                        'from' => $from));
            }
        }

        $title = __("trans:translate");

        $body = view_layout("one_column_wide", view_title($title), implode("<hr><br>", $area2));

        $this->page_draw($title,$body);
    }

    function action_save_translation()
    {
        $this->require_login();
        $this->validate_security_token();

        $text = get_input('translation');
        $guid = get_input('entity_guid');
        $isHTML = (int)get_input('html');
        $property = get_input('property');
        $entity = get_entity($guid);

        if (!$entity->can_edit())
        {
            register_error(__("org:cantedit"));
            forward_to_referrer();
        }
        else
        {
            $origLang = $entity->get_language();

            $actualOrigLang = get_input('language');
            $newLang = get_input('newLang');

            if ($actualOrigLang != $origLang)
            {
                $entity->language = $actualOrigLang;
                $entity->save();
            }
            if ($actualOrigLang != $newLang)
            {
                $trans = $entity->lookup_translation($property, $actualOrigLang, $newLang, TranslateMode::ManualOnly, $isHTML);
                
                if (get_input('delete'))
                {
                    $trans->delete();
                }
                else
                {                
                    $trans->html = $isHTML;
                    $trans->owner_guid = Session::get_loggedin_userid();
                    $trans->value = $text;
                    $trans->save();
                }
            }

            system_message(__("trans:posted"));

            forward(get_input('from') ?: $entity->get_url());
        }
    }

    function action_translate_interface()
    {
        $this->require_login();

        if (get_input('exception'))
        {
            throw new Exception("test exception!");
        }

        $lang = 'sw';

        load_translation($lang);

        $key = get_input('key');

        if ($key)
        {
            $title = __("trans:item_title");
            $body = view_layout("one_column_padded", view_title($title), view("translation/interface_item", array('lang' => $lang, 'key' => $key)));
            $this->page_draw($title, $body);
        }
        else if (get_input('export'))
        {
            header("Content-type: text/plain");
            echo view("translation/interface_export", array('lang' => $lang));
        }
        else
        {
            $title = __("trans:list_title");
            $body = view_layout("one_column_wide", view_title($title), view("translation/interface_list", array('lang' => $lang)));
            $this->page_draw($title, $body);
        }
    }

    function action_save_interface_item()
    {
        $this->require_login();
        $this->validate_security_token();

        $key = get_input('key');
        $value = get_input('value');
        $lang = 'sw';

        $trans = InterfaceTranslation::getByKeyAndLang($key, $lang);

        if (!$trans)
        {
            $trans = new InterfaceTranslation();
            $trans->key = $key;
            $trans->lang = $lang;
        }

        $trans->approval = 0;
        $trans->owner_guid = Session::get_loggedin_userid();
        $trans->value = $value;
        $trans->save();

        system_message(__("trans:posted"));

        forward(get_input('from'));
    }
    
    function action_featured()
    {
        PageContext::set_translatable(false);
        $title = __('featured:title');
        $body = view('org/featured');
        $this->page_draw($title, view_layout("one_column_padded", view_title($title), $body));
    }
}