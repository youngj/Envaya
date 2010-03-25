<?php    

    set_context('login');
    $body = elgg_view_layout('one_column_padded', elgg_view_title(elgg_echo("login")), elgg_view("account/forms/login"));
           
    page_draw(elgg_echo("login"), $body);
