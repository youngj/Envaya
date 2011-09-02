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
        Session::logout();
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

    function action_password_reset_code()
    {
        $action = new Action_PasswordResetCode($this);
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

    function action_receive_mail()
    {
        // query string in sendgrid should have ?secret=<sendgrid_secret>
        // http://www.quora.com/What-are-the-security-models-of-SendGrid-APInbox-and-CloudMailin/answer/Tim-Falls
        $secret = @$_REQUEST['secret'];
      
        if ($secret !== Config::get('sendgrid_secret'))
        {
            $this->set_status(403);
            $this->set_content("Invalid request secret");
            throw new RequestAbortedException();        
        }
        
        $mail = new IncomingMail();
        
        // this is the format for sendgrid parse api -- todo: generalize like action_receive_sms
        $mail->subject = $_REQUEST['subject'];
        $mail->to = $_REQUEST['to'];
        $mail->text = $_REQUEST['text'];
        $mail->from = $_REQUEST['from'];
        
        $attachment_info = json_decode(@$_REQUEST['attachment-info'], true);
        
        foreach ($_FILES as $name => $file)
        {                
            $type = @$attachment_info[$name]['type'];
        
            if ($type)
            {
                $file['type'] = $type;
            }        
            $mail->add_attachment($file);
        }
        
        $mail->process();
    }
    
    function action_receive_sms()
    {        
        $provider = SMS::get_provider();
    
        if (!$provider->is_validated_request())
        {
            $this->set_status(403);
            $this->set_content("Invalid request signature");
            throw new RequestAbortedException();
        }
    
        $request = $provider->init_request();        
        
        $initial_controller = $request->get_initial_controller();
        
        $initial_controller->set_request($request);
        
        $controller = $initial_controller->execute($request->get_message()) ?: $initial_controller;
        
        $request->save_state();
        
        $this->set_content_type('text/xml');
        $this->set_content($controller->get_response_xml());
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
        $storage_local = Storage::get_instance();

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
        $this->set_header('Cache-Control', 'max-age=86400');
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
            
            $revisions = ContentRevision::query_drafts($entity)->filter();        
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
        $this->set_header('Cache-Control', 'max-age=86400');
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
    
        $this->page_draw(array(
            'title' => __('browse:title'),
            'content' => view("org/change_filter", array(
                'baseurl' => '/pg/browse', 
                'filters' => Query_Filter::filters_from_input(array('Sector','Country','Region'))
            ), 'mobile')
        ));
    }        
    
    function action_search()
    {
        $this->prefer_http();
        $this->allow_view_types(null);
    
        $q = get_input('q');
        
        $filters = Query_Filter::filters_from_input(array('Sector'));
        
        $vars = array('query' => $q, 'filters' => $filters);
        
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
                $region = GeoIP::get_country_code();
                if (Geography::is_available_country($region))
                {
                    // bias geocode result to user's country
                }
                else
                { 
                    // bias geocode result to default country
                    $country_codes = Config::get('available_countries');
                    $region = $country_codes[0];
                }
            
                $latlong = Geography::geocode($q, $region);
            }
            
            if ($latlong)
            {
				$query = Organization::query()
                    ->apply_filters($filters)
                    ->in_area(
                        $latlong['lat'] - 1.0, 
                        $latlong['long'] - 1.0, 
                        $latlong['lat'] + 1.0, 
                        $latlong['long'] + 1.0
                    );
                
				$vars['nearby'] = ($query->limit(1)->get() != null);            
                $vars['latlong'] = $latlong;
            }            

            $vars['results'] = view('org/search_list', array(
                'fulltext' => $q,
                'filters' => $filters,
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
        $phone_numbers = PhoneNumber::canonicalize_multi(get_input('phone_number'));
        
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
            $last_digits = array_map(function($pn) { return UserPhoneNumber::get_last_digits($pn); }, $phone_numbers);
            
            $phone_number_entities = UserPhoneNumber::query()->where_in('last_digits', $last_digits)->filter();        
            
            $user_guids = array_map(function($p) { return $p->user_guid; }, $phone_number_entities);            
            
            $orgs_by_phone = Organization::query()->where_in('guid', $user_guids)->filter();
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
        
        $this->page_draw(array(
            'title' => __("feed:title"),
            'content' => view("org/change_filter", array(
                'baseurl' => '/pg/feed', 
                'filters' => Query_Filter::filters_from_input(array('Sector','Country','Region'))
            ), 'mobile')
        ));
    }

    function action_feed()
    {
        $this->prefer_http();
        $this->allow_view_types('rss');
    
        $max_items = Config::get('feed_page_size');
        
        $filters = Query_Filter::filters_from_input(array('Sector','Country','Region'));                        
        $feedName = FeedItem::feed_name_from_filters($filters);
        $items = FeedItem::query_by_feed_name($feedName)
            ->where_visible_to_user()
            ->limit($max_items)
            ->filter();

        $this->page_draw(array(
            'title' => __("feed:title"),
            'content' => view("org/feed", array(
                'filters' => $filters,
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

        $filters = Query_Filter::filters_from_input(array('Sector','Country','Region'));                        
        $feedName = FeedItem::feed_name_from_filters($filters);        
        $before_id = (int)get_input('before_id');
        
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

    function action_js_orgs()
    {     
        $this->set_content_type('text/javascript');     
        
        $guids = explode(',', get_input('ids'));
        
        $orgs = Organization::query()
            ->where_in('guid', $guids)
            ->columns('guid,subtype_id,latitude,longitude,username,name')
            ->order_by('name')
            
            ->filter();
        
        $js_orgs = array_map(function($org) { return $org->js_properties(); }, $orgs);
        
        $this->set_content(json_encode($js_orgs));
    }
    
    function action_search_area()
    {
        $this->set_content_type('text/javascript');
        
        $lat_min = get_input('lat_min');
        $lat_max = get_input('lat_max');
        $long_min = get_input('long_min');
        $long_max = get_input('long_max');
        $sector = get_input('sector');               

        $query = Organization::query();
        
        $query->in_area($lat_min, $long_min, $lat_max, $long_max);        
        $query->with_sector($sector);                                        
        $query->where_visible_to_user();        
        $query->columns('guid,subtype_id,latitude,longitude');

        $orgs = $query->filter();

        $bucketizer = new Map_Bucketizer(array(
            'px_width' => (int)get_input('width') ?: 1,
            'px_height' => (int)get_input('height') ?: 1,
            'lat_min' => $lat_min,
            'lat_max' => $lat_max,
            'long_min' => $long_min,
            'long_max' => $long_max,
        ));
        
        $this->set_content(json_encode(
            $bucketizer->get_buckets($orgs)
        ));
    }
}

Controller_Pg::$routes = Controller::$SIMPLE_ROUTES;