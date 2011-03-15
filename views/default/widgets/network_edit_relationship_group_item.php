<?php

    $relationship = $vars['relationship'];
    $widget = $vars['widget'];
    $confirmDelete = $relationship->__('confirm_delete');

    $org = $relationship->get_subject_organization();
    $name = $relationship->get_subject_name();    
    $url = $relationship->get_subject_url();

    $link_open = $url ? "<a href='".escape($url)."'>" : '';
    $link_close = $url ? "</a>" : '';            
    
    echo "<div class='search_listing'>";
    
    echo "<div style='float:right;'>";
    
    if (!$relationship->is_self_approved())
    {
        $url = view('output/post_url', array('href' => "{$widget->get_edit_url()}?action=approve_relationship&guid={$relationship->guid}"));    
        echo "<a href='$url'>".__('network:approve')."</a> &middot; ";
    }
    
    echo "<a href='{$widget->get_edit_url()}?action=edit_relationship&guid={$relationship->guid}'>".__('edit')."</a> &middot; ";
       
    echo view('output/confirmlink', array(
            'text' => __('delete'),
            'confirm' => sprintf($confirmDelete, $relationship->get_subject_name()),
            'href' => "{$widget->get_edit_url()}?action=delete_relationship&guid={$relationship->guid}",
            'is_action' => true,
    ));
    
    echo "</div>";
        
    echo "<div>";
    
    echo "<b>$link_open".escape($name)."$link_close</b>";
    
    if (!$relationship->is_self_approved())
    {
        echo " <em>(".__('network:awaiting_approval').")</em>";
    }
    
    echo "</div>";

    if (!$relationship->subject_guid && $relationship->subject_email)
    {
        echo "<div>".view('output/email', array('value' => $relationship->subject_email))."</div>";
    }

    if ($relationship->content)
    {
        $content = $relationship->render_content();
        echo "<div class='feed_snippet'>".Markup::get_snippet($content, 200)."</div>";
    }
    else
    {
        echo "<div class='feed_snippet'>"
                . "<a href='{$widget->get_edit_url()}?action=edit_relationship&guid={$relationship->guid}'>"
                . "<em>".__('network:add_description')."</em>"
                . "</a></div>";
    }                       
    
    echo "</div>";