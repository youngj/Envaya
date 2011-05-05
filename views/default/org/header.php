<?php
    $org = $vars['org'];
    
    $url = escape($org->get_url());
    
    if ($org->get_design_setting('custom_header'))
    {
        $header_image = $org->get_design_setting('header_image');    

        if ($header_image)
        {
            $width = escape(@$header_image['width']);
            $height = escape(@$header_image['height']);    
            $imgUrl = escape($header_image['url']);
            
            echo "<div style='text-align:center;height:{$height}px'><a href='$url'><img width='$width' height='$height' src='$imgUrl' alt='$escTitle' /></a></div>";
        }    
    }
    else
    {
        echo "<table id='heading' style='width:100%'><tr>";    

        $logo = view('org/icon', array('org' => $org, 'size' => 'medium'));  
        if ($logo)
        {
            echo "<td style='width:80px'><a href='$url'>$logo</a></td>";       
        }
        echo "<td>";
        echo "<h2 class='withicon'><a href='$url'>".escape($org->name)."</a></h2>";

        $tagline = $org->get_design_setting('tagline');
        if ($tagline)
        {
            echo "<h3 class='withicon'>".escape($tagline)."</h3>";
        }        
        echo "</td>";    
        
        $share_links = $org->get_design_setting('share_links');
        
        if (is_array($share_links) && sizeof($share_links) > 0) 
        {         
            echo "<td>";       
            echo "<div class='shareLinks'>";

            // no point in sharing a site that isn't publicly visible on facebook/twitter. 
            // but sharing by email is okay because emails are held until the org is approved
            $onclick = $org->is_approved() ? 'ignoreDirty()' : 'alert('.json_encode(__('approval:waiting')).'); return false';
                        
            if (in_array('email', $share_links))
            {
                echo "<a rel='nofollow' href='javascript:emailShare(".json_encode($org->username).")' onclick='ignoreDirty()'>";
                echo "<span class='shareIcon' style='background-position:-36px -1px'></span>";
                echo "<span class='shareText'>".__('share:email')."</span>";
                echo "</a>";
            }
            
            if (in_array('facebook', $share_links))
            {
                echo "<a rel='nofollow' href='javascript:fbShare()' onclick='$onclick'>";
                echo "<span class='shareIcon' style='padding-bottom:3px'></span>";
                echo "<span class='shareText' style='padding-top:2px'>".__('share:facebook')."</span>";
                echo "</a>";
            }
            
            if (in_array('twitter', $share_links))
            {
                echo "<a rel='nofollow' href='javascript:twitterShare()' onclick='$onclick'>";
                echo "<span class='shareIcon' style='background-position:-18px top'></span>";
                echo "<span class='shareText' style='padding-top:1px'>".__('share:twitter')."</span>";
                echo "</a>";
            }
            
            echo "</div>";
            echo "</td>";
        }
        echo "</tr></table>";
    }