<?php

/*
 * Controller for a wide variety of actions that don't fit anywhere else.
 *
 * URL: /pg/<action>
 */
class Controller_Pg extends Controller 
{
    static $routes; // initialized at bottom of file
    
    function action_login()
    {
        $action = new Action_Login($this);
        $action->execute();   
    }
    
    function action_logout()
    {
        logout();
        $this->redirect('/');
    }    
    
    function action_register()
    {
        $action = new Action_Register($this);
        $action->execute();
    }
    
    function action_dashboard()
    {
        $this->require_login();
        $this->redirect(Session::get_loggedin_user()->get_url()."/dashboard");
    }

    function action_forgot_password()
    {
        $action = new Action_ForgotPassword($this);
        $action->execute();
    }
    
    function action_password_reset()
    {
        $action = new Action_PasswordReset($this);
        $action->execute();    
    }

    function action_upload()
    {
        $action = new Action_Upload($this);
        $action->execute();
    }

    function action_blank()
    {
        $this->allow_view_types(null);
        // this may be useful for displaying a page containing only SessionMessages
        $this->page_draw(array(
            'no_top_bar' => true, 
            'layout' => 'layouts/frame', 
            'no_top_bar' => true,            
            'content' => SessionMessages::view_all(),
            'header' => ''
        ));
    }

    function action_large_img()
    {
        $owner_guid = get_input('owner');
        $group_name = get_input('group');

        $largeFile = UploadedFile::query()->where('owner_guid = ?', $owner_guid)->where('group_name = ?', $group_name)
            ->order_by('width desc')->get();

        if ($largeFile)
        {
            echo "<html><body><img src='{$largeFile->get_url()}' width='{$largeFile->width}' height='{$largeFile->height}' /></body></html>";
        }
        else
        {
            throw new NotFoundException();
        }
    }

    function action_receive_sms()
    {
        $from = @$_REQUEST['From'];
        $body = @$_REQUEST['Body'];

        error_log("SMS received:\n from=$from body=$body");

        if ($from && $body)
        {
            $sms_request = new SMS_Request($from, $body);
            $sms_request->execute();
        }
        else
        {
            throw new NotFoundException();
        }
    }

    function action_delete_comment()
    {
        $guid = (int)get_input('comment');
        $comment = Comment::get_by_guid($guid);
        if ($comment && $comment->can_edit())
        {
            $comment->disable();
            $comment->save();

            $container = $comment->get_container_entity();
            $container->num_comments = $container->query_comments()->count();
            $container->save();

            SessionMessages::add(__('comment:deleted'));
            
            $this->redirect($container->get_url());
        }
        else
        {
            throw new RedirectException(__('comment:not_deleted'));
        }
    }

    function action_local_store()
    {
        // not for use in production environment
        $storage_local = get_storage();

        if (!($storage_local instanceof Storage_Local))
        {
            throw new NotFoundException();
        }

        $path = get_input('path');

        $components = explode('/', $path);

        foreach ($components as $component)
        {
            if (preg_match('/[^\w\.\-]|(\.\.)/', $component))
            {
                throw new NotFoundException();
            }
        }

        $local_path = $storage_local->get_file_path(implode('/', $components));

        if (!is_file($local_path))
        {
            throw new NotFoundException();
        }

        $mime_type = UploadedFile::get_mime_type($local_path);
        $filename = $components[sizeof($components) - 1];
        
        if ($mime_type && in_array($mime_type, array('text/plain','application/pdf','image/jpeg','image/png','image/gif')))
        {
            // okay to show in browser
        }   
        else
        {
            // possibly dangerous to show in browser; show as download
            $this->set_header('Content-Disposition', "attachment; filename=\"$filename\"");
        }        
        $this->set_content_type($mime_type);
        $this->set_content(file_get_contents($local_path));
    }
    
    function action_hide_todo()
    {
        Session::set('hide_todo', 1);
        
        $this->set_content_type('text/javascript');
        $this->set_content(json_encode("OK"));    
    }
    
    function action_change_lang()
    {
        $url = @$_GET['url'];
        $newLang = $_GET['lang'];
        // $this->change_viewer_language($newLang); // unnecessary because done in default controller
        Session::save_input();
        $this->redirect(url_with_param($url, 'lang', $newLang));
    }
    
    function action_js_revision_content()
    {
        $this->set_content_type('text/javascript');
        
        $id = (int)get_input('id');
        
        $revision = ContentRevision::query()->where('id = ?', $id)->get();
        if (!$revision || !$revision->can_edit())
        {
            throw new SecurityException("Access denied.");
        }
        
        $this->set_content(json_encode(array(
            'content' => $revision->content
        )));
    }
    
    function action_js_revisions()
    {
        $this->set_content_type('text/javascript');
        
        $entity_guid = (int)get_input('entity_guid');
        
        $entity = Entity::get_by_guid($entity_guid, true);
        if (!$entity)
        {
            $revisions = array();
        }
        else
        {
            if (!$entity->can_edit())
            {
                throw new SecurityException("Access denied.");
            }
            
            $revisions = ContentRevision::query()->where('entity_guid = ?', $entity_guid)->order_by('time_updated desc')->filter();        
        }
        
        $this->set_content(json_encode(array(
            'revisions' => array_map(function($r) { return $r->js_properties(); }, $revisions),
        )));
    }

    function action_select_image()
    {
        $file = UploadedFile::get_from_url(get_input('src'));

        $this->allow_view_types(null);
        $this->page_draw(array(
            'layout' => 'layouts/frame',
            'no_top_bar' => true,            
            'content' => view('upload/select_image', array(
                'current' => $file,
                'position' => get_input('pos'),
                'frameId' => get_input('frameId'),
            ))
        ));
    }
    
    function action_select_document()
    {
        $guid = (int)get_input('guid');
        $file = UploadedFile::get_by_guid($guid);
        
        $this->allow_view_types(null);
        $this->page_draw(array(
            'layout' => 'layouts/frame',
            'no_top_bar' => true,
            'content' => view('upload/select_document', array(
                'current' => $file,
                'frameId' => get_input('frameId'),
            ))
        ));        
    }    
    
    function action_confirm_action()
    {
        $action = new Action_ConfirmAction($this);
        $action->execute();
    }
    
    function action_show_captcha()
    {
        Captcha::show();
    }

    function action_email_settings()
    {
        $action = new Action_EmailSettings($this);
        $action->execute();   
    }        

    function action_delete_feed_item()
    {
        $this->validate_security_token();
        $feedItem = FeedItem::query()->where('id = ?', (int)get_input('item'))->get();
        
        if (!$feedItem || !$feedItem->can_edit())
        {
            throw new RedirectException(__('page:notfound:details'));
        }
        
        foreach ($feedItem->query_items_in_group()->filter() as $item)
        {
            $item->delete();
        }           
        SessionMessages::add(__('feed:item_deleted'));
        $this->redirect();
    }   
    
    /*
     * Web entry point for uncompressed CSS files (for testing).     
     */
    function action_css()
    {
        $name = get_input('name');
        
        if (preg_match('/[^\w]/', $name))
        {
            throw new NotFoundException();
        }
        try
        {
            $css = view('css/'.($name ?: 'default'));
        }
        catch (InvalidParameterException $ex)
        {
            throw new NotFoundException();
        }
        
        $this->set_content_type('text/css');        
        $this->set_content($css);
    }

    function action_discussions()
    {
        $this->allow_view_types(null);
        
        $this->page_draw(array(
            'title' => __('discussions:latest'),
            'content' => view('discussions/topic_list')
        ));                    
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
            'content' => view("org/change_filter", array('baseurl' => '/pg/browse', 'sector' => $sector, 'region' => $region), 'mobile')
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
                    $country_codes = Geography::get_supported_countries();
                    $region = $country_codes[0];
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
                
        $this->set_content(json_encode(array(
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
                'baseurl' => '/pg/feed', 
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
        
        $this->set_content(json_encode(array(
            'items_html' => $items_html,
            'first_id' => $this->get_first_item_id($items, $max_items)
        )));
    }

    function action_search_area()
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

        $this->set_content(json_encode($orgJs));
    }        
}

Controller_Pg::$routes = Controller::$SIMPLE_ROUTES;