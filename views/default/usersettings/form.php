<?php

	$form_body = elgg_view("usersettings/user");
	
    echo elgg_view('input/form', 
        array('enctype' => 'multipart/form-data',  'action' => "{$vars['url']}action/usersettings/save", 
        'body' => $form_body));
?>