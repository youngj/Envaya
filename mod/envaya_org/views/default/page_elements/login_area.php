<?php
    if (Session::is_logged_in())
    {
        echo view('page_elements/loggedin_area', $vars);
    }
    else
    {
        echo view('page_elements/login_button', $vars);
    }