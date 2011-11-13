<?php
    $share_links = @$vars['design']['share_links'];
    
    if (is_array($share_links) && sizeof($share_links) > 0) 
    {         
        echo view('js/share');
        echo "<div class='shareLinks'>";

        // no point in sharing a site that isn't publicly visible on facebook/twitter. 
        // but sharing by email is okay because emails are held until the org is approved
        $onclick = $vars['site_approved'] ? 'ignoreDirty()' : 'alert('.json_encode(__('approval:waiting')).'); return false';
                    
        if (in_array('email', $share_links))
        {
            echo "<a style='background-position:right -5px;height:18px' rel='nofollow' href='javascript:emailShare("
                .json_encode($vars['site_username']).")' onclick='ignoreDirty()'>";
            echo __('share:email');
            echo "</a>";
        }
        
        if (in_array('facebook', $share_links))
        {
            echo "<a style='background-position:right -36px' rel='nofollow' href='javascript:fbShare()' onclick='$onclick'>";
            echo __('share:facebook');
            echo "</a>";
        }
        
        if (in_array('twitter', $share_links))
        {
            echo "<a style='background-position:right -65px' rel='nofollow' href='javascript:twitterShare()' onclick='$onclick'>";
            echo __('share:twitter');
            echo "</a>";
        }
        
        echo "</div>";
    }
