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
<?php
    $partnership = $loggedInOrg->get_partnership($org);

    if (!$partnership->is_self_approved() && !$partnership->is_partner_approved())
    {
        echo view('output/confirmlink', array(
            'text' => __('partner:request'),
            'is_action' => true,
            'href' => "{$org->get_url()}/request_partner"
        ));
    }
    else if (!$partnership->is_self_approved())
    {
        echo view('output/confirmlink', array(
            'text' => __('partner:approve'),
            'href' => $org->get_partnership($loggedInOrg)->get_approve_url()
        ));
    }
    else if (!$partnership->is_partner_approved())
    {
        echo __('partner:pending');

        echo "&nbsp;";

        echo view('output/confirmlink', array(
            'text' => "(".__('partner:re_request').")",
            'is_action' => true,
            'href' => "{$org->get_url()}/request_partner"
        ));
    }
    else
    {
        echo __('partner:exists');
    }
?>
</td>
<td class='commBoxMain' style='border-left:1px solid gray'>
<a href='<?php echo $org->get_url() ?>/compose'><?php echo __('message:link'); ?></a>
</td>
<td class='commBoxRight'>
&nbsp;
</td>
</table>
<?php
}
?>