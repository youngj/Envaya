<?php

    $org = $vars['entity'];
    $loggedInOrg = Session::get_loggedin_user();

    if ($loggedInOrg instanceof Organization && $org->email)
    {

?>

<table class='commBox'>
<tr>
<td class='commBoxLeft'>
&nbsp;
</td>
<td class='commBoxMain'>
<a href='<?php echo $org->get_url() ?>/compose'><?php echo __('message:link'); ?></a>
</td>
<td class='commBoxRight'>
&nbsp;
</td>
</table>
<?php
}
?>