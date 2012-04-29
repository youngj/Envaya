<?php
    $user = Session::get_logged_in_user();
    if ($user)
    {
        echo "<div id='top_right'>";
    
        echo "<span id='top_whoami'>";
        echo "{$user->username}";
        echo "</span>";
    
        if ($user && $user->is_setup_complete())
        {
            $url = $user->get_url();                 
            
            if (Permission_EditUserSite::has_for_entity($user))
            {
                echo "<a href='{$url}'>".__('view_site')."</a>";               
                echo "<a href='{$url}/dashboard'>".__('edit_site')."</a>";
            }
            else
            {
                echo "<a href='{$url}/dashboard'>".__('user:self_dashboard')."</a>";
            }    
            echo "<a href='{$url}/settings'>".__('settings')."</a>";                                            
            
            echo view('input/post_link', array(
                    'href' => '/pg/logout',        
                    'html' => __("logout"),
            ));      
        }               
        
        echo "</div>";
    }
    else if (!@$vars['hide_login'])
    {
        echo "<div style='float:right'>";
        echo view('page_elements/login_button', $vars);
        echo "</div>";
    }