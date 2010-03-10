
<?php    

    $body = elgg_view_layout('one_column', elgg_view_title(elgg_echo("login")), elgg_view("account/forms/login"));
           
    page_draw(elgg_echo("login"), $body);

?>
