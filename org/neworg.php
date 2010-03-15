<?php
    $step = ((int) get_input('step')) ?: 1;
    
    if ($step > 3)
    {
        $step = 1;
    }
    
    if ($step == 2 && !$_SESSION['registration'])
    {
        register_error(elgg_echo("qualify:missing"));
        $step = 1;
    }
    
    if ($step == 3 && !isloggedin())
    {
        register_error(elgg_echo("create:notloggedin"));
        $step = 1;
        forward('pg/login');
    }
    
    if (isloggedin() && $step < 3)
    {
        $step = 3;
    }
    
	$title = elgg_echo("register:title");	
    $body = elgg_view_layout('one_column_padded', elgg_view_title($title), elgg_view("org/register$step"));	
	page_draw($title, $body);
?>