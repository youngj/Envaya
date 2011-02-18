<?php
/**
 * Abstract controller class. Controllers should only be created using a [Request].
 *
 * Controllers methods will be automatically called in the following order by
 * the request:
 *
 *     $controller = new Controller_Foo($request);
 *     $controller->before();
 *     $controller->action_bar();
 *     $controller->after();
 *
 * The controller action should add the output it creates to
 * `$this->request->response`, typically in the form of a [View], during the
 * "action" part of execution.
 *
 * @package    Kohana
 * @category   Controller
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
abstract class Controller {

    /**
     * @var  object  Request that created the controller
     */
    public $request;
    
    protected $page_draw_vars = array();

    /**
     * Creates a new controller instance. Each controller must be constructed
     * with the request object that created it.
     *
     * @param   object  Request that created the controller
     * @return  void
     */
    public function __construct(Request $request)
    {
        // Assign the request to the controller
        $this->request = $request;
    }

    public function page_draw($title, $body, $vars = null)
    {    
        if (get_input('__topbar') == '0')
        {
            $this->page_draw_vars['no_top_bar'] = true;
        }
    
        $this->request->response = page_draw($title, $body, $vars ?: $this->page_draw_vars);
    }
    
    public function add_generic_footer()
    {
        PageContext::add_submenu_item(__('about'), "/envaya", 'footer');
        PageContext::add_submenu_item(__('contact'), "/envaya/contact", 'footer');
        PageContext::add_submenu_item(__('donate'), "/envaya/page/contribute", 'footer');    
    }

    public function validate_security_token()
    {
        try
        {
            validate_security_token();
        }
        catch (SecurityException $ex)
        {
            action_error($ex->getMessage());
            forward();
            exit;
        }
        
        $user = Session::get_loggedin_user();
        if ($user)
        {

            $user->last_action = time();
            $user->save();
        }        
    }
    
    public function require_http()
    {
        if (Request::$protocol == 'https')
        {
            $url = Request::full_original_url();
            $url = str_replace("https://", "http://", $url);
            forward($url);
        }
    }
    
    public function require_https()
    {
        if (Request::$protocol == 'http' && Config::get('ssl_enabled') && !is_mobile_browser())
        {
            $url = secure_url(Request::full_original_url());
            forward($url);
        }
    }
    
    public function require_login()
    {
        if (!Session::isloggedin())
        {
            force_login();
        }
    }

    public function require_admin()
    {
        if (!Session::isadminloggedin())
        {
            if (Session::isloggedin())
            {
                register_error(__('noaccess'));
            }
        
            force_login();
        }
    }

    /**
     * Automatically executed before the controller action. Can be used to set
     * class properties, do authorization checks, and execute other custom code.
     *
     * @return  void
     */
    public function before()
    {
        // Nothing by default
    }

    /**
     * Automatically executed after the controller action. Can be used to apply
     * transformation to the request response, add extra output, and execute
     * other custom code.
     *
     * @return  void
     */
    public function after()
    {
    }

    function process_create_account_form()
    {        
        $name = trim(get_input('org_name'));

        if (!$name)
        {
            throw new RegistrationException(__('create:no_name'));
        }

        $username = trim(get_input('username'));

        validate_username($username);

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

        if (!get_input('ignore_possible_duplicates'))
        {
            $dups = Organization::query(true)
                ->where("(username = ? OR (email = ? AND ? <> '') OR INSTR(name,?) > 0 OR INSTR(?,name) > 0)", 
                    $username, $email, $email, $name, $name)
                ->filter();  
                
            if (sizeof($dups) > 0)
            {
                throw new PossibleDuplicateException(__('create:possible_duplicate'), $dups);
            }
        }
        
        if (get_user_by_username($username))
        {
            throw new RegistrationException(__('create:username_exists'));
        }                
        
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
        $org->setup_state = SetupState::CreatedAccount;

        //$org->registration_number = $prevInfo['registration_number'];
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

        system_message(__("create:ok"));   

        return $org;        
    }
    
    function show_possible_duplicate($ex, $login_url = '/pg/login')
    {
        $title = __("create:possible_duplicate");
        $body = view_layout('one_column', 
            view_title($title, array('org_only' => true)), 
            view("org/possible_duplicate", array('message' => $ex->getMessage(), 'login_url' => $login_url, 'duplicates' => $ex->duplicates))
        );
        $this->page_draw($title, $body);
    }
    
    function process_create_profile_form()
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
        
        $org->setup_state = SetupState::CreatedHomePage;
        $org->save();

        if ($prevSetupState < $org->setup_state && !$org->is_approved())
        {
            post_feed_items($org, 'register', $org);

            send_admin_mail(sprintf(__('email:registernotify:subject'), $org->name), 
                sprintf(__('email:registernotify:body'), $org->get_url().'?login=1')
            );
        }            
        
        system_message(__("setup:ok"));                
        
        return $org;
    }    
    
    
} // End Controller
