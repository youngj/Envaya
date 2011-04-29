<?php        
    $org = $vars['org'];
    $title = $vars['title'];    
    
    echo view('page_elements/title', array(
        'title' => $vars['title'],
        'sitename' => $org->name,
        'logo' => view('org/icon', array('org' => $org, 'size' => 'medium')),
        'title_url' => $org->get_url()
    ));
