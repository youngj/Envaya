<?php   
    if (isset($vars['design']))
    {
        echo view('js/share');
        echo "<div class='shareLinks'>";

        // no point in sharing a site that isn't publicly visible on facebook/twitter. 
        // but sharing by email is okay because emails are held until the org is approved
        $onclick = $vars['site_approved'] ? 'ignoreDirty()' : 'alert('.json_encode(__('approval:waiting')).'); return false';
        
        echo "<span style='font-weight:bold'>";
        echo __('share:share');
        echo "</span> &nbsp;&nbsp;&nbsp;  ";
        
        echo "<a style='background-position:left -5px;height:18px' rel='nofollow' href='javascript:emailShare("
            .json_encode($vars['site_username']).")' onclick='ignoreDirty()'>";
        echo __('share:email');
        echo "</a>";
        
        echo "<a style='background-position:left -36px' rel='nofollow' href='javascript:fbShare()' onclick='$onclick'>";
        echo __('share:facebook');
        echo "</a>";
        
        echo "<a style='background-position:left -65px' rel='nofollow' href='javascript:twitterShare()' onclick='$onclick'>";
        echo __('share:twitter');
        echo "</a>";
        
        echo "<a style='background-position:left -95px;padding-left:28px' rel='nofollow' href='javascript:googlePlusShare()' onclick='$onclick'>";
        echo __('share:googleplus');
        echo "</a>";        
        
        echo "</div>";
    }