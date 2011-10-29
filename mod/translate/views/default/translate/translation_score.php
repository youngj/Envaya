<?php
    $translation = $vars['translation'];
        
    $user = Session::get_logged_in_user();
    
    if ($user)
    {
        $vote = $translation->query_votes()->where('owner_guid = ?', $user->guid)->get();

        $is_language_admin = Permission_ManageLanguage::has_for_entity($translation->get_language());
        
        $can_upvote = !$vote || $vote->score <= 0 || $is_language_admin;
        $can_downvote = !$vote || $vote->score >= 0 || $is_language_admin;
    
        if ($can_downvote)
        {        
            echo " ".view('input/post_link', array(
                'href' => "{$translation->get_url()}/vote?delta=-1",
                'style' => "background-color:#ccc;color:#000;text-decoration:none",
                'html' => "&nbsp;-&nbsp;",
            ))." ";
        }
        else
        {
            echo " <span style='background-color:#d64046;color:white;'>&nbsp;-&nbsp;</span> ";
        }
        echo "<strong>{$translation->score}</strong>";
        if ($can_upvote)
        {
            echo " ".view('input/post_link', array(
                'href' => "{$translation->get_url()}/vote?delta=1",
                'style' => "background-color:#ccc;color:#000;text-decoration:none",
                'html' => "&nbsp;+&nbsp;",
            ))." ";            
        }
        else
        {
            echo " <span style='background-color:#4690d6;color:white;'>&nbsp;+&nbsp;</span> ";
        }
    }
