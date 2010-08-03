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
            $area = elgg_view("org/browseList", array('lat' => $lat, 'long' => $long, 'sector' => $sector));
        }
        else
        {
            $lat = get_input('lat');
            $long = get_input('long');
            $zoom = get_input('zoom');

            $area = elgg_view("org/browseMap", array('lat' => $lat, 'long' => $long, 'zoom' => $zoom, 'sector' => $sector));
        }

        $body = elgg_view_layout('one_column', elgg_view_title($title), $area);

        $this->page_draw($title, $body);
    }

    function action_search()
    {
        set_context('search');

        $query = get_input('q');

        if ($query)
        {
            $title = sprintf(__('search:title_with_query'),$query);
        }
        else
        {
            $title = __('search:title');
        }
        $content = elgg_view('org/search', array('query' => $query, 'sector' => get_input('sector')));

        $body = elgg_view_layout('one_column_padded', elgg_view_title(__('search:title')), $content);

        $this->page_draw($title,$body);
    }

    function action_feed()
    {
        $title = __("feed:title");

        $area = elgg_view("org/feed");

        page_set_translatable(false);

        $body = elgg_view_layout('one_column', elgg_view_title($title), $area);

        $this->page_draw($title, $body);
    }

    function action_new()
    {
        $step = ((int) get_input('step')) ?: 1;

        if ($step > 3)
        {
            $step = 1;
        }

        $loggedInUser = get_loggedin_user();

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
        $body = elgg_view_layout('one_column', elgg_view_title($title, array('org_only' => true)), elgg_view("org/register$step"));
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

            access_show_hidden_entities(true);

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
            $org->salt = generate_random_cleartext_password();
            $org->password = generate_user_password($org, $password);
            $org->owner_guid = 0;
            $org->container_guid = 0;
            $org->language = get_language();
            $org->theme = "green";
            $org->setup_state = 3;

            $prevInfo = Session::get('registration');

            //$org->registration_number = $prevInfo['registration_number'];
            $org->country = $prevInfo['country'];
            //$org->local = $prevInfo['local'];

            $org->setLatLong(-6.140555,35.551758);

            $org->save();

            /* auto-create empty pages */
            $org->getWidgetByName('news')->save();
            $org->getWidgetByName('team')->save();
            $org->getWidgetByName('projects')->save();
            $org->getWidgetByName('history')->save();
            $org->getWidgetByName('partnerships')->save();

            $contactWidget = $org->getWidgetByName('contact');
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
            $org = get_loggedin_user();

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

            $homeWidget = $org->getWidgetByName('home');
            $homeWidget->setContent($mission, true);

            $org->language = get_input('content_language');

            $org->setSectors($sectors);
            $org->city = get_input('city');
            $org->region = get_input('region');
            $org->sector_other = get_input('sector_other');

            $org->theme = get_input('theme');

            $latlong = Geocoder::geocode($org->getLocationText());

            if ($latlong)
            {
                $org->setLatLong($latlong['lat'], $latlong['long']);
            }

            $homeWidget->save();

            $prevSetupState = $org->setup_state;
            
            $org->setup_state = 5;
            $org->save();

            if ($prevSetupState < $org->setup_state && !$org->isApproved())
            {
                post_feed_items($org, 'register', $org);

                send_admin_mail(sprintf(__('email:registernotify:subject'), $org->name), 
                    sprintf(__('email:registernotify:body'), $org->getURL().'?login=1')
                );
            }            
            
            system_message(__("setup:ok"));
            

            forward($org->getUrl());
        }
        catch (RegistrationException $r)
        {
            action_error($r->getMessage());
        }
    }

    function action_searchArea()
    {
        set_context('search');

        $latMin = get_input('latMin');
        $latMax = get_input('latMax');
        $longMin = get_input('longMin');
        $longMax = get_input('longMax');
        $sector = get_input('sector');

        $orgs = Organization::queryByArea(array($latMin, $longMin, $latMax, $longMax), $sector)->filter();

        $orgJs = array();

        foreach ($orgs as $org)
        {
            $orgJs[] = $org->jsProperties();
        }

        $this->request->headers['Content-Type'] = 'text/javascript';
        $this->request->response = json_encode($orgJs);
    }

    function action_emailSettings()
    {
        $email = get_input('e');
        $code = get_input('c');
        $users = get_users_by_email($email);

        $title = __("user:notification:label");

        if ($email && $code == get_email_fingerprint($email) && sizeof($users) > 0)
        {
            $area1 = elgg_view('org/emailSettings', array('email' => $email, 'users' => $users));
        }
        else
        {
            $area1 = __("user:notification:invalid");
        }
        $body = elgg_view_layout("one_column_padded", elgg_view_title($title), $area1);

        $this->page_draw($title, $body);
    }

    function action_emailSettings_save()
    {
        $email = get_input('email');
        $code = get_input('code');
        $notify_days = get_input('notify_days');
        $users = get_users_by_email($email);

        foreach ($users as $user)
        {
            $user->notify_days = $notify_days;
            $user->save();

            system_message(__('user:notification:success'));
        }

        forward("/");
    }

    function action_selectImage()
    {
        set_input("__topbar",0);

        $file = get_file_from_url(get_input('src'));

        $content = elgg_view('org/selectImage',
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
        set_theme('editor');

        $props = get_input_array("prop");
        $from = get_input('from');

        $area2 = array();

        foreach ($props as $propStr)
        {
            $guidProp = explode('.', $propStr);
            $guid = $guidProp[0];
            $prop = $guidProp[1];
            $isHTML = (int)$guidProp[2];

            $entity = get_entity($guid);

            if ($entity && $entity->canEdit() && $entity->get($prop))
            {
                $area2[] = elgg_view("translation/translate",
                    array(
                        'entity' => $entity,
                        'property' => $prop,
                        'isHTML' => $isHTML,
                        'from' => $from));
            }
        }

        $title = __("trans:translate");

        $body = elgg_view_layout("one_column_wide", elgg_view_title($title), implode("<hr><br>", $area2));

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

        if (!$entity->canEdit())
        {
            register_error(__("org:cantedit"));
            forward_to_referrer();
        }
        else if (empty($text))
        {
            register_error(__("trans:empty"));
            forward_to_referrer();
        }
        else
        {
            $origLang = $entity->getLanguage();

            $actualOrigLang = get_input('language');
            $newLang = get_input('newLang');

            if ($actualOrigLang != $origLang)
            {
                $entity->language = $actualOrigLang;
                $entity->save();
            }
            if ($actualOrigLang != $newLang)
            {
                $trans = lookup_translation($entity, $property, $actualOrigLang, $newLang, TranslateMode::ManualOnly, $isHTML);
                if (!$trans)
                {
                    $trans = new Translation();
                    $trans->container_guid = $entity->guid;
                    $trans->property = $property;
                    $trans->lang = $newLang;
                }
                $trans->html = $isHTML;
                $trans->owner_guid = get_loggedin_userid();
                $trans->value = $text;
                $trans->save();
            }

            system_message(__("trans:posted"));

            forward(get_input('from') ?: $entity->getUrl());
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
            $body = elgg_view_layout("one_column_padded", elgg_view_title($title), elgg_view("translation/interface_item", array('lang' => $lang, 'key' => $key)));
            $this->page_draw($title, $body);
        }
        else if (get_input('export'))
        {
            header("Content-type: text/plain");
            echo elgg_view("translation/interface_export", array('lang' => $lang));
        }
        else
        {
            $title = __("trans:list_title");
            $body = elgg_view_layout("one_column_wide", elgg_view_title($title), elgg_view("translation/interface_list", array('lang' => $lang)));
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
        $trans->owner_guid = get_loggedin_userid();
        $trans->value = $value;
        $trans->save();

        system_message(__("trans:posted"));

        forward(get_input('from'));
    }
    
    function action_featured()
    {
        page_set_translatable(false);
        $title = __('featured:title');
        $body = elgg_view('org/featured');
        $this->page_draw($title, elgg_view_layout("one_column_padded", elgg_view_title($title), $body));
    }
}