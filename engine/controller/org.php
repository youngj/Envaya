<?php

/*
 * Controller for a variety of global/public pages relating to organizations
 *
 * URL: /org/<action>
 */
 
class Controller_Org extends Controller
{
    static $routes; // initialized at bottom of file

    function before()
    {
        $this->add_generic_footer();
    }

    function action_browse()
    {
        $this->prefer_http();
        $this->allow_view_types(null);
    
        $content = view('org/browse');

        $this->page_draw(array(
            'title' => __('browse:title'),
            'content' => $content
        ));
    }

    function action_browse_email()
    {
        $this->require_login();
        $this->allow_view_types(null);
    
        $content = view('org/browse_email');

        $this->page_draw(array(
            'title' => __('browse:title'),
            'layout' => 'layouts/frame',
            'no_top_bar' => true,
            'content' => $content
        ));
    }    
    
    function action_change_browse_view()
    {
        $this->allow_view_types(null);
    
        $sector = get_input('sector');
        $region = get_input('region');
        
        $this->page_draw(array(
            'title' => __('browse:title'),
            'content' => view("org/change_filter", array('baseurl' => '/org/browse', 'sector' => $sector, 'region' => $region), 'mobile')
        ));
    }        
    
    function action_search()
    {
        $this->prefer_http();
        $this->allow_view_types(null);
    
        $q = get_input('q');
        $sector = get_input('sector');
        
        $vars = array('query' => $q, 'sector' => $sector);
        
        if (empty($q) && !$sector)
        {        
            $title = __('search:title');
            $content = view('org/search', $vars);
        }
        else
        {
            $title = __('search:results');            
            
            if (!empty($q)) 
            {
                if (GeoIP::is_supported_country())
                {
                    // bias geocode result to user's country
                    $region = GeoIP::get_country_code();
                }
                else
                { 
                    // bias geocode result to default country
                    $approved_countries = Geography::get_approved_countries();
                    $region = $approved_countries[0];
                }
            
                $latlong = Geography::geocode($q, $region);
            }
            
            if ($latlong)
            {
				$query = Organization::query();
				$query->in_area(
					$latlong['lat'] - 1.0, 
					$latlong['long'] - 1.0, 
					$latlong['lat'] + 1.0, 
					$latlong['long'] + 1.0
				);
				if ($sector)
				{
					$query->with_sector($sector);
				}
				$vars['nearby'] = ($query->limit(1)->get() != null);
            
                $vars['latlong'] = $latlong;
            }            

            $vars['results'] = view('org/search_list', array(
                'fulltext' => $q,
                'sector' => $sector,
            ));
            
            $content = view('org/search_results', $vars);
        }

        $this->page_draw(array(
            'title' => $title,
            'content' => $content
        ));
    }

    function action_js_search()
    {
        $this->set_content_type('text/javascript');
    
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
            
            $orgs_by_phone = Organization::query()->where_in('guid', $org_guids)->filter();
        }
        
        // if there's a likely unique match by website or email, avoid searching by name
        // (where we are likely to get some bad matches)
        if (sizeof($orgs_by_website) != 1 && sizeof($orgs_by_email) != 1) 
        {            
            if ($name)
            {
                $orgs_by_name = Organization::query()->fulltext($name)->limit(2)->filter();
            }
        }
            
        $all_orgs = array_merge($orgs_by_website, $orgs_by_email, $orgs_by_phone, $orgs_by_name);
        
        // remove duplicates
        $all_orgs_map = array();
        foreach ($all_orgs as $org)
        {
            $all_orgs_map[$org->guid] = $org;
        }
        $all_orgs = array_values($all_orgs_map);
                
        $this->set_response(json_encode(array(
            'can_invite' => InvitedEmail::get_by_email($email)->can_send_invite(),
            'results' => array_map(function($o) { 
                return array(
                    'org' => $o->js_properties(),
                    'view' => view('org/js_search_result', array('org' => $o))
                );
            }, $all_orgs),
        )));                
    }   
    
    function action_change_feed_view()
    {
        $this->allow_view_types(null);
        
        $sector = get_input('sector');
        $region = get_input('region');
        
        $this->page_draw(array(
            'title' => __("feed:title"),
            'content' => view("org/change_filter", array(
                'baseurl' => '/org/feed', 
                'sector' => $sector, 
                'region' => $region
            ), 'mobile')
        ));
    }

    function action_feed()
    {
        $this->prefer_http();
        $this->allow_view_types('rss');
    
        $max_items = 20;
        
        $sector = get_input('sector');
        $region = get_input('region');
        $feedName = FeedItem::make_feed_name(array('sector' => $sector, 'region' => $region));
        $items = FeedItem::query_by_feed_name($feedName)
            ->where_visible_to_user()
            ->limit($max_items)
            ->filter();

        $this->page_draw(array(
            'title' => __("feed:title"),
            'content' => view("org/feed", array(
                'sector' => $sector, 
                'region' => $region, 
                'items' => $items,
                'first_id' => $this->get_first_item_id($items, $max_items)
            ))
        ));
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
        $this->set_content_type('text/javascript');

        $sector = get_input('sector');
        $region = get_input('region');
        $before_id = (int)get_input('before_id');
        $feedName = FeedItem::make_feed_name(array('sector' => $sector, 'region' => $region));
        
        $max_items = 20;
        
        $items = FeedItem::query_by_feed_name($feedName)
            ->where_visible_to_user()
            ->where('id < ?', $before_id)
            ->limit($max_items)
            ->filter();

        $items_html = view('feed/list', array('items' => $items));
        
        $this->set_response(json_encode(array(
            'items_html' => $items_html,
            'first_id' => $this->get_first_item_id($items, $max_items)
        )));
    }

    function action_new()
    {
        $this->allow_view_types(null);
    
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
            throw new RedirectException('', "org/new?invite=".urlencode($invite_code));
        }

        if ($step == 3 && !$loggedInUser)
        {
            return $this->force_login();
        }

        if ($loggedInUser  && $step < 3)
        {
            $step = 3;
        }

        if ($step == 2 && !Session::get('registration'))
        {
            SessionMessages::add_error(__('register:qualify_missing'));
            $step = 1;
        }

        $this->page_draw(array(
            'title' => __("register:title"),
            'content' => view("org/register$step"),
            'org_only' => true
        ));
    }

    function action_register1()
    {
        $action = new Action_Registration_Qualification($this);
        $action->execute();    
    }  
    
    function action_register2()
    {
        $action = new Action_Registration_CreateAccount($this);
        $action->execute();
    }       

    function action_register3()
    {
        $action = new Action_Registration_CreateProfile($this);
        $action->execute();
    }

    function action_searchArea()
    {
        $this->set_content_type('text/javascript');
    
        $latMin = get_input('latMin');
        $latMax = get_input('latMax');
        $longMin = get_input('longMin');
        $longMax = get_input('longMax');
        $sector = get_input('sector');

        $query = Organization::query();
        
        $query->in_area($latMin, $longMin, $latMax, $longMax);        
        if ($sector)
        {
            $query->with_sector($sector);                                        
        }
        $query->where_visible_to_user();        
        $query->columns('guid,subtype_id,username,name,latitude,longitude');

        $orgs = $query->filter();

        $orgJs = array();

        foreach ($orgs as $org)
        {
            $orgJs[] = $org->js_properties();
        }

        $this->set_response(json_encode($orgJs));
    }
    
    function action_featured()
    {
        $this->allow_view_types(null);

        $this->page_draw(array(
            'title' => __('featured:title'),
            'content' => view('org/featured')
        ));
    }    
}

Controller_Org::$routes = Controller::$SIMPLE_ROUTES;