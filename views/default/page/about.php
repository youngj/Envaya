<?php

    $org = get_user_by_username('envaya');
    $home = $org->getWidgetByName('home');
    echo $home->renderContent();

?>

<p style='margin-bottom:0px'>
<?php echo elgg_echo('about:donate') ?>
</p>

<?php echo elgg_view('org/donate_button') ?>
