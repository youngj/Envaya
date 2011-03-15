<?php    
    $relationship = $vars['entity'];      
    $org = $relationship->get_subject_organization();
        
    $name = $relationship->get_subject_name();    
    $url = $relationship->get_subject_url();
    
    $link_open = $url ? "<a href='".escape($url)."'>" : '';
    $link_close = $url ? "</a>" : '';        

    if ($org && $org->custom_icon)
    {            
        echo "<div class='image_right'>"
            .$link_open
            .view('graphics/icon', array('entity' => $org, 'size' => 'medium'))
            .$link_close
            ."</div>";
    }
    echo "<h3 id='r{$relationship->guid}'>$link_open".escape($name)."$link_close</h3>";        
    echo $relationship->render_content();
