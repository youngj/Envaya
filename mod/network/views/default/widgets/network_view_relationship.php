<?php    
    $relationship = $vars['relationship'];      
    $org = $relationship->get_subject_organization();
        
    $name = $relationship->get_subject_name();    
    $url = $relationship->get_subject_url();
    
    $link_open = $url ? "<a href='".escape($url)."'>" : '';
    $link_close = $url ? "</a>" : '';        

    if ($org && $org->has_custom_icon())
    {            
        echo "<div class='image_right'>"
            .$link_open
            .view('org/icon', array('org' => $org, 'style' => "width:40px"))
            .$link_close
            ."</div>";
    }
    echo "<h3 id='r{$relationship->guid}'>$link_open".escape($name)."$link_close</h3>";        

    echo view('widgets/network_view_relationship_contact', array('relationship' => $relationship));
    
    echo $relationship->render_content();
