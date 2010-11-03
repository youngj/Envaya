<?php
    if (Session::isloggedin())
    {
        echo view('page_elements/loggedin_area');
    }
    else
    {
        global $CONFIG;
        $loginUrl = (@$vars['loginToCurrentPage']) 
            ? url_with_param(Request::instance()->full_rewritten_url(), 'login',1) 
            : "{$CONFIG->secure_url}pg/login";    
        echo view('page_elements/login_button', array('login_url' => $loginUrl));
    }