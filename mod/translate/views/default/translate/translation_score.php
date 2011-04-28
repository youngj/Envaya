<?php
    $translation = $vars['translation'];
        
    $user = Session::get_loggedin_user();
    
    if ($user)
    {
        $vote = $translation->query_votes()->where('owner_guid = ?', $user->guid)->get();
        
        $can_upvote = !$vote || $vote->score <= 0 || $user->admin;
        $can_downvote = !$vote || $vote->score >= 0 || $user->admin;
    
        if ($can_downvote)
        {        
            $href = view('output/post_url', array(
                'href' => "{$translation->get_url()}/vote?delta=-1",
            ));
            echo " <a style='background-color:#ccc;color:#000;text-decoration:none' href='$href'>&nbsp;-&nbsp;</a> ";
        }
        else
        {
            echo " <span style='background-color:#d64046;color:white;'>&nbsp;-&nbsp;</span> ";
        }
        echo "<strong>{$translation->score}</strong>";
        if ($can_upvote)
        {
            $href = view('output/post_url', array(
                'href' => "{$translation->get_url()}/vote?delta=1",
            ));
            echo " <a style='background-color:#ccc;color:#000;text-decoration:none' href='$href'>&nbsp;+&nbsp;</a>";
        }
        else
        {
            echo " <span style='background-color:#4690d6;color:white;'>&nbsp;+&nbsp;</span> ";
        }
    }
