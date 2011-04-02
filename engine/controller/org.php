<?php

class Controller_Org extends Controller
{
    function before()
    {
        $this->add_generic_footer();
    }

    function action_browse()
    {
        $this->require_http();
    
        $title = __("browse:title");
        $sector = get_input('sector');
        
        $list = get_input("list");
        
        if ($list || get_viewtype() == 'mobile')
        {
            $area = view("org/browseList", array('lat' => $lat, 'long' => $long, 'sector' => $sector, 'region' => get_input('region')));
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

    function action_change_browse_view()
    {
        $sector = get_input('sector');
        $region = get_input('region');
        
        $area = view("org/change_filter", array('baseurl' => '/org/browse', 'sector' => $sector, 'region' => $region), 'mobile');
        
        $title = __("browse:title");
        $body = view_layout('one_column', view_title($title), $area);
        $this->page_draw($title, $body);       
    }        
    
    function action_search()
    {
        $this->require_http();
    
        $query = get_input('q');
        $sector = get_input('sector');
        
        $vars = array('query' => $query, 'sector' => $sector);
        
        if (empty($query) && !$sector)
        {        
            $title = __('search:title');
            $content = view('org/search', $vars);
        }
        else
        {
            $title = __('search:results');            
            
            if (!empty($query)) 
            {
                $geoQuery = "$query Tanzania";            
                $latlong = Geocoder::geocode($geoQuery);
            }
            
            if ($latlong)
            {
                $vars['nearby'] = Organization::query_by_area(
                    array(
                        $latlong['lat'] - 1.0, 
                        $latlong['long'] - 1.0, 
                        $latlong['lat'] + 1.0, 
                        $latlong['long'] + 1.0
                    ),    
                    $sector)->limit(1)->get() != null;
                $vars['latlong'] = $latlong;
            }            

            $vars['results'] = Organization::list_search($query, $sector, $region=null, $limit = 10);            
            
            $content = view('org/search_results', $vars);
        }

        $body = view_layout('one_column', view_title($title), $content);
        $this->page_draw($title,$body);
    }

    function action_js_search()
    {
        $this->request->headers['Content-Type'] = 'text/javascript';                
    
        $name = get_input('name');
        $email = get_input('email');
        $website = get_input('website');
        $phone_numbers = OrgPhoneNumber::split_phone_number(get_input('phone_number'));
        
        $orgs_by_name = $orgs_by_email = $orgs_by_website = $orgs_by_phone = array();       
        
        if ($email)
        {
            $orgs_by_email = Organization::query()->where('email = ?', $email)->filter();
        }
        
        if ($website)
        {
            $username = null;
            $parsed_website = parse_url($website);                
            if ($parsed_website)            
            {
                $host = @$parsed_website['host'];
                $username = OrgDomainName::get_username_for_host($host);                
            }
            if (!$username && preg_match('/\/([\w\-]+)/', $parsed_website['path'], $matches))
            {
                $username = $matches[1];
            }
            if ($username)
            {
                $orgs_by_website = Organization::query()->where('username = ?', $username)->filter();
            }
        }        
        
        if (sizeof($phone_numbers) > 0)
        {
            $last_digits = array_map(function($pn) { return OrgPhoneNumber::get_last_digits($pn); }, $phone_numbers);
            
            $phone_number_entities = OrgPhoneNumber::query()->where_in('last_digits', $last_digits)->filter();        
            
            $org_guids = array_map(function($p) { return $p->org_guid; }, $phone_number_entities);            
            
            $orgs_by_phone = Organization::query()->where_in('e.guid', $org_guids)->filter();
        }
        
        // if there's a likely unique match by website or email, avoid searching by name
        // (where we are likely to get some bad matches)
        if (sizeof($orgs_by_website) != 1 && sizeof($orgs_by_email) != 1) 
        {            
            if ($name)
            {
                $orgs_by_name = Organization::query_search($name)->limit(2)->filter();
            }
        }
            
        $all_orgs = array_merge($orgs_by_website, $orgs_by_email, $orgs_by_phone, $orgs_by_name);
        
        // remove duplicates
        $all_orgs = array_values(array_combine(
            array_map(function($o) { return $o->guid; }, $all_orgs),
            $all_orgs
        ) ?: array());
                
        $this->request->response = json_encode(array(
            'can_invite' => InvitedEmail::get_by_email($email)->can_send_invite(),
            'results' => array_map(function($o) { 
                return array(
                    'org' => $o->js_properties(),
                    'view' => view('org/js_search_result', array('org' => $o))
                );
            }, $all_orgs),
        ));                
    }   
    
    function action_change_feed_view()
    {
        $sector = get_input('sector');
        $region = get_input('region');
        
        $area = view("org/change_filter", array('baseurl' => '/org/feed', 'sector' => $sector, 'region' => $region), 'mobile');
        
        $title = __("feed:title");
        $body = view_layout('one_column', view_title($title), $area);
        $this->page_draw($title, $body);       
    }

    function action_feed()
    {
        $this->require_http();
    
        $title = __("feed:title");
        
        $max_items = 20;
        
        $sector = get_input('sector');
        $region = get_input('region');
        $feedName = get_feed_name(array('sector' => $sector, 'region' => $region));
        $items = FeedItem::query_by_feed_name($feedName)->limit($max_items)->filter();

        $area = view("org/feed", array(
            'sector' => $sector, 
            'region' => $region, 
            'items' => $items,
            'first_id' => $this->get_first_item_id($items, $max_items)
        ));

        PageContext::set_rss(true);
        PageContext::set_translatable(false);

        $body = view_layout('one_column', view_title($title), $area);

        $this->page_draw($title, $body);
    }
    
    private function get_first_item_id($items, $num_requested)
    {
        if (sizeof($items) < $num_requested)
        {
            return 0;
        }
        $first_id = null;
        foreach ($items as $item)
        {
            if (is_null($first_id) || $item->id < $first_id)
            {
                $first_id = $item->id;
            }
        }           
        return $first_id;
    }
    
    function action_feed_more()
    {
        $sector = get_input('sector');
        $region = get_input('region');
        $before_id = (int)get_input('before_id');
        $feedName = get_feed_name(array('sector' => $sector, 'region' => $region));
        
        $max_items = 20;
        
        $items = FeedItem::query_by_feed_name($feedName)->where('id < ?', $before_id)->limit($max_items)->filter();

        $items_html = view('feed/list', array('items' => $items));
        
        $this->request->headers['Content-Type'] = 'text/javascript';
        $this->request->response = json_encode(array(
            'items_html' => $items_html,
            'first_id' => $this->get_first_item_id($items, $max_items)
        ));
    }

    function action_new()
    {
        $invite_code = get_input('invite');
        if ($invite_code)
        {
            Session::set('invite_code', $invite_code);
        }
    
        $step = ((int) get_input('step')) ?: 1;        
        if ($step > 3)
        {
            $step = 1;
        }

        $loggedInUser = Session::get_loggedin_user();

        if ($loggedInUser && !($loggedInUser instanceof Organization))
        {
            logout();
            forward("org/new?invite=".urlencode($invite_code));
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
        $action = new Action_Registration_Qualification($this);
        $action->process_input();    
    }  
    
    function action_register2()
    {
        $action = new Action_Registration_CreateAccount($this);
        $action->process_input();
    }       

    function action_register3()
    {
        $action = new Action_Registration_CreateProfile($this);
        $action->process_input();
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
        $action = new Action_EmailSettings($this);
        $action->execute();   
    }
    
    function action_featured()
    {
        PageContext::set_translatable(false);
        $title = __('featured:title');
        $body = view('org/featured');
        $this->page_draw($title, view_layout("one_column", view_title($title), $body));
    }
}