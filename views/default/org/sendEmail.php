
<form action='action/org/sendEmail' method='POST'>

<?php echo elgg_view('input/securitytoken'); ?>

<?php

echo elgg_view('input/submit',array(
    'value' => elgg_echo('message:send')
));
?>

</form>