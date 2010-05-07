<?php

    $org = $vars['entity'];
    $loggedInOrg = get_loggedin_user();

    if ($loggedInOrg instanceof Organization)
    {

?>

<table class='commBox'>
<tr>
<td class='commBoxLeft'>
&nbsp;
</td>
<td class='commBoxMain'>
<?php
    $partnership = $loggedInOrg->getPartnership($org);

    if (!$partnership->isSelfApproved() && !$partnership->isPartnerApproved())
    {
        echo elgg_view('output/confirmlink', array(
            'text' => elgg_echo('partner:request'),
            'is_action' => true,
            'href' => "action/org/requestPartner?partner_guid={$org->guid}"
        ));
    }
    else if (!$partnership->isSelfApproved())
    {
        echo elgg_view('output/confirmlink', array(
            'text' => elgg_echo('partner:approve'),
            'href' => $org->getPartnership($loggedInOrg)->getApproveUrl()
        ));
    }
    else if (!$partnership->isPartnerApproved())
    {
        echo elgg_echo('partner:pending');

        echo "&nbsp;";

        echo elgg_view('output/confirmlink', array(
            'text' => "(".elgg_echo('partner:re_request').")",
            'is_action' => true,
            'href' => "action/org/requestPartner?partner_guid={$org->guid}"
        ));
    }
    else
    {
        echo elgg_echo('partner:exists');
    }
?>
</td>
<td class='commBoxMain' style='border-left:1px solid gray'>
<a href='<?php echo $org->getURL() ?>/compose'><?php echo elgg_echo('message:link'); ?></a>
</td>
<td class='commBoxRight'>
&nbsp;
</td>
</table>
<?php
}
?>