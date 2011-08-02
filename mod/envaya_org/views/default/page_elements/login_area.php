<?php
    if (Session::isloggedin())
    {
        echo view('page_elements/loggedin_area', $vars);
    }
    else
    {
        echo view('page_elements/login_button', $vars);
    }