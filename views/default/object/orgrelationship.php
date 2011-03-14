<?php    
    $relationship = $vars['entity'];      
    $org = $relationship->get_subject_organization();
    
    if ($org)
    {
        echo view_entity($org);
    }
    else
    {    
        $icon = view('graphics/icon', array(
            'entity' => $relationship,
            'size' => 'small',
        ));

        $name = $relationship->get_subject_name();
        
        $url = $relationship->get_subject_url();
        $link_open = $url ? "<a href='".escape($url)."'>" : '';
        $link_close = $url ? "</a>" : '';

        $info = "<div><b>$link_open".escape($name)."$link_close</b></div>";
        
        if (!$relationship->subject_guid && $relationship->subject_email)
        {
            $info .= "<div>".view('output/email', array('value' => $relationship->subject_email))."</div>";
        }
        
        $icon = "$link_open$icon$link_close";

        echo view('search/listing',array('icon' => $icon, 'info' => $info));
    }
