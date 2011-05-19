<?php

    $relationship = $vars['relationship'];
    $widget = $vars['widget'];

    $org = $relationship->get_subject_organization();
    $name = $relationship->get_subject_name();    
    $url = $relationship->get_subject_url();

    $link_open = $url ? "<a href='".escape($url)."'>" : '';
    $link_close = $url ? "</a>" : '';            
    
    echo "<div class='search_listing'>";
    
    echo "<div style='float:right;'>";
    
    echo "<a href='{$widget->get_edit_url()}?action=edit_relationship&guid={$relationship->guid}'>".__('edit')."</a> &middot; ";
       
    echo view('input/post_link', array(
            'text' => __('delete'),
            'confirm' => strtr(__('network:confirm_delete'), array(
                '{name}' => $relationship->get_subject_name(),
                '{type}' => $relationship->msg('header')
            )),
            'href' => "{$widget->get_edit_url()}?action=delete_relationship&guid={$relationship->guid}",
    ));
    
    echo "</div>";
        
    echo "<div>";
    
    echo "<b>$link_open".escape($name)."$link_close</b>";
        
    echo "</div>";

    echo view('widgets/network_view_relationship_contact', array('relationship' => $relationship));   

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