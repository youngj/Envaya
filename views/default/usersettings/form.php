<?php

	$form_body = "<div class=\"user_settings\">" . elgg_view("usersettings/user") . "</div>";
	
    echo elgg_view('input/form', array('enctype' => 'multipart/form-data',  'action' => "{$vars['url']}action/usersettings/save", 'body' => $form_body));
?>