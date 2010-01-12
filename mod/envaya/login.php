
<?php    

    $body = elgg_view_layout('one_column', elgg_view("account/forms/login"),"");
           
    page_draw(elgg_echo("login"), $body);

?>
