<?php

class Controller_Org extends Controller
{
    function before()
    {
        add_generic_footer();
    }

    function action_browse()
    {
        $title = elgg_echo("browse:title");
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
            $title = sprintf(elgg_echo('search:title_with_query'),$query);
        }
        else
        {
            $title = elgg_echo('search:title');
        }
        $content = elgg_view('org/search', array('query' => $query, 'sector' => get_input('sector')));

        $body = elgg_view_layout('one_column_padded', elgg_view_title(elgg_echo('search:title')), $content);

        $this->page_draw($title,$body);
    }

    function action_feed()
    {
        $title = elgg_echo("feed:title");

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
            register_error(elgg_echo("create:notloggedin"));
            $step = 1;
            forward('pg/login');
        }

        if ($loggedInUser  && $step < 3)
        {
            $step = 3;
        }

        if ($step == 2 && !Session::get('registration'))
        {
            register_error(elgg_echo("qualify:missing"));
            $step = 1;
        }

        $title = elgg_echo("register:title");
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
                throw new RegistrationException(elgg_echo("qualify:wrong_country"));
            }

            $orgType = get_input('org_type');
            if ($orgType != 'np')
            {
                throw new RegistrationException(elgg_echo("qualify:wrong_org_type"));
            }

            Session::set('registration', array(
                //'registration_number' => get_input('registration_number'),
                'country' => get_input('country'),
            ));

            system_message(elgg_echo("qualify:ok"));
            forward("org/new?step=2");

        }
        catch (RegistrationException $r)
        {
            action_error($r->getMessage());
        }
    }

    function action_register2()
    {
        action_gatekeeper();

        try
        {
            $name = trim(get_input('org_name'));

            if (!$name)
            {
                throw new RegistrationException(elgg_echo('create:no_name'));
            }

            $username = trim(get_input('username'));

            validate_username($username);

            access_show_hidden_entities(true);

            if (get_user_by_username($username))
            {
                throw new RegistrationException(elgg_echo('create:username_exists'));
            }


            $password = get_input('password');
            $password2 = get_input('password2');

            if (strcmp($password, $password2) != 0)
            {
                throw new RegistrationException(elgg_echo('create:passwords_differ'));
            }

            $lpassword = strtolower($password);
            $lusername = strtolower($username);
            $lname = strtolower($name);

            if (strpos($lname, $lpassword) !== FALSE || strpos($lusername, $lpassword) !== FALSE)
            {
                throw new RegistrationException(elgg_echo('create:password_too_easy'));
            }

            validate_password($password);

            $email = trim(get_input('email'));

            validate_email_address($email);

            $org = new Organization();
            $org->username = $username;
            $org->phone_number = get_input('phone');
            $org->email = $email;
            $org->name = $name;
            $org->access_id = ACCESS_PUBLIC;
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
            if (email)
            {
                $contactWidget->public_email = "yes";
            }
            $contactWidget->save();

            $guid = $org->guid;

            login($org, false);

            Session::set('registration', null);

            system_message(sprintf(elgg_echo("create:ok"),$CONFIG->sitename));

            forward("org/new?step=3");
        }
        catch (RegistrationException $r)
        {
            action_error($r->getMessage());
        }
    }

    function action_register3()
    {
        gatekeeper();
        action_gatekeeper();

        try
        {
            $org = get_loggedin_user();

            $mission = get_input('mission');
            if (!$mission)
            {
                throw new RegistrationException(elgg_echo("setup:mission:blank"));
            }

            $sectors = get_input_array('sector');
            if (sizeof($sectors) == 0)
            {
                throw new RegistrationException(elgg_echo("setup:sector:blank"));
            }
            else if (sizeof($sectors) > 5)
            {
                throw new RegistrationException(elgg_echo("setup:sector:toomany"));
            }

            $homeWidget = $org->getWidgetByName('home');
            $homeWidget->setContent($mission, false);

            $org->language = get_input('content_language');

            $org->setSectors($sectors);
            $org->city = get_input('city');
            $org->region = get_input('region');
            $org->sector_other = get_input('sector_other');

            $org->theme = get_input('theme');

            $latlong = elgg_geocode_location($org->getLocationText());

            if ($latlong)
            {
                $org->setLatLong($latlong['lat'], $latlong['long']);
            }

            $homeWidget->save();

            $org->setup_state = 5;
            $org->save();

            system_message(elgg_echo("setup:ok"));

            trigger_elgg_event('register', 'organization', $org);

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

        $orgs = Organization::filterByArea(array($latMin, $longMin, $latMax, $longMax), $sector, $limit = 1000);

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

        $title = elgg_echo("user:notification:label");

        if ($email && $code == get_email_fingerprint($email) && sizeof($users) > 0)
        {
            $area1 = elgg_view('org/emailSettings', array('email' => $email, 'users' => $users));
        }
        else
        {
            $area1 = elgg_echo("user:notification:invalid");
        }
        $body = elgg_view_layout("one_column_padded", elgg_view_title($title), $area1);

        $this->page_draw($title, $body);
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
        admin_gatekeeper();
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

        $title = elgg_echo("trans:translate");

        $body = elgg_view_layout("one_column_wide", elgg_view_title($title), implode("<hr><br>", $area2));

        $this->page_draw($title,$body);
    }
}