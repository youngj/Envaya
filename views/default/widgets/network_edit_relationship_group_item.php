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
    
    if (!$relationship->is_self_approved())
    {
        $url = view('output/post_url', array('href' => "{$widget->get_edit_url()}?action=approve&guid={$relationship->guid}"));    
        echo "<a href='$url'>".__('network:add')."</a> &middot; ";
    }
    
    echo "<a href='{$widget->get_edit_url()}?action=edit_relationship&guid={$relationship->guid}'>".__('edit')."</a> &middot; ";
       
    echo view('output/confirmlink', array(
            'text' => __('delete'),
            'confirm' => sprintf($relationship->__('confirm_delete'), $relationship->get_subject_name()),
            'href' => "{$widget->get_edit_url()}?action=delete_relationship&guid={$relationship->guid}",
            'is_action' => true,
    ));
    
    echo "</div>";
        
    echo "<div>";
    
    echo "<b>$link_open".escape($name)."$link_close</b>";
    
    if (!$relationship->is_self_approved())
    {
        echo " <em>(".__('network:suggestion').")</em>";
    }
    
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