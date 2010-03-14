<?php

	$form_body = "<div class=\"user_settings\">" . elgg_view("usersettings/user") . " ";
	$form_body .= "<p>" . elgg_view('input/submit', array('value' => elgg_echo('save'))) . "</p></div>";

    echo elgg_view('input/form', array('enctype' => 'multipart/form-data',  'action' => "{$vars['url']}action/usersettings/save", 'body' => $form_body));
?>