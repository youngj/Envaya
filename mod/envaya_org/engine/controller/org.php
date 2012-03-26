<?php

/*
 * Controller for a variety of global/public pages specific to envaya.org
 *
 * URL: /org/<action>
 */
 
class Controller_Org extends Controller
{
    static $routes; // initialized at bottom of file

    function before()
    {
        $footer = PageContext::get_submenu('footer');
    
        $footer->add_link(__('about'), "/envaya");
        $footer->add_link(__('contact'), "/envaya/contact");
        $footer->add_link(__('donate'), "/envaya/page/contribute");    
    }    

    function action_hide_todo()
    {
        Permission_Public::require_any();    
    
        Session::set('hide_todo', 1);
        
        $this->set_content_type('text/javascript');
        $this->set_content(json_encode("OK"));    
    }
            
    function action_tci_donate_frame()
    {
        Permission_Public::require_any();    
        $this->set_content(view("page/tci_donate_frame"));
    }

    function action_submit_donate_form()
    {
        Permission_Public::require_any();    
    
        $values = $_POST;
        $amount = (int)@$values['_amount'] ?: (int)@$values['_other_amount'];
        $values['amount'] = $amount;

        $emailBody = "";

        foreach ($values as $k => $v)
        {
            $emailBody .= "$k = $v\n\n";
        }

        OutgoingMail::create("Donation form started", $emailBody)->send_to_admin();

        if (!$amount)
        {
            throw new RedirectException("Please select a donation amount.");
        }
        if (!$values['full_name'])
        {
            throw new RedirectException("Please enter your Full Name.");
        }
        if (!$values['phone'])
        {
            throw new RedirectException("Please enter your Phone Number.");
        }
        if (!$values['email'])
        {
            throw new RedirectException("Please enter your Email Address.");
        }

        $full_name = $values['full_name'];
        $last_space = strrpos($full_name, ' ');
        if ($last_space !== false)
        {
            $values['firstname'] = substr($full_name, 0, $last_space);
            $values['lastname'] = substr($full_name, $last_space + 1);        
        }
        else
        {
            $values['firstname'] = $full_name;
            $values['lastname'] = $full_name;
        }
        
        unset($values['full_name']);                
        unset($values['_amount']);
        unset($values['_other_amount']);

        $this->set_content(view("page/submit_tci_donate_form", $values));
    }
    
    function action_send_feedback()
    {
        $action = new Action_Contact($this);
        $action->execute();
    }    

    function action_new()
    {
        $action = new Action_Registration_Qualification($this);
        $action->execute();        
    }    
     
    function action_create_account()
    {
        $action = new Action_Registration_CreateAccount($this);
        $action->execute();    
    }
    
    function action_create_profile()
    {
        $action = new Action_Registration_CreateProfile($this);
        $action->execute();
    }   

    function action_featured()
    {
        Permission_Public::require_any();    
        $this->allow_view_types(null);

        $this->allow_content_translation();
        $this->page_draw_vars['hide_translate_bar'] = true;        
        
        $this->page_draw(array(
            'title' => __('featured:title'),
            'content' => view('org/featured_sites')
        ));
    }        
}

Controller_Org::$routes = Controller::$SIMPLE_ROUTES;