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
    
        $footer->add_item(__('about'), "/envaya");
        $footer->add_item(__('contact'), "/envaya/contact");
        $footer->add_item(__('donate'), "/envaya/page/contribute");    
    }    

    function action_tci_donate_frame()
    {
        $this->set_content(view("page/tci_donate_frame"));
    }

    function action_submit_donate_form()
    {
        $values = $_POST;
        $amount = (int)$values['_amount'] ?: (int)$values['_other_amount'];
        $values['donation'] = $amount;

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
        if (!$values['Name'])
        {
            throw new RedirectException("Please enter your Full Name.");
        }
        if (!$values['phone'])
        {
            throw new RedirectException("Please enter your Phone Number.");
        }
        if (!$values['Email'])
        {
            throw new RedirectException("Please enter your Email Address.");
        }

        unset($values['_amount']);
        unset($values['_other_amount']);
        unset($values['Submit']);

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
    
    function action_register_logged_in()
    {
        $action = new Action_Registration_LoggedIn($this);
        $action->execute();
    }

    function action_featured()
    {
        $this->allow_view_types(null);

        $this->allow_content_translation();
        $this->page_draw_vars['hide_translate_bar'] = true;        
        
        $this->page_draw(array(
            'title' => __('featured:title'),
            'content' => view('org/featured')
        ));
    }    
    
}

Controller_Org::$routes = Controller::$SIMPLE_ROUTES;