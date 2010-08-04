<?php

    $org = $vars['entity'];
    $loggedInOrg = get_loggedin_user();

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
    $partnership = $loggedInOrg->getPartnership($org);

    if (!$partnership->isSelfApproved() && !$partnership->isPartnerApproved())
    {
        echo view('output/confirmlink', array(
            'text' => __('partner:request'),
            'is_action' => true,
            'href' => "{$org->getURL()}/request_partner"
        ));
    }
    else if (!$partnership->isSelfApproved())
    {
        echo view('output/confirmlink', array(
            'text' => __('partner:approve'),
            'href' => $org->getPartnership($loggedInOrg)->getApproveUrl()
        ));
    }
    else if (!$partnership->isPartnerApproved())
    {
        echo __('partner:pending');

        echo "&nbsp;";

        echo view('output/confirmlink', array(
            'text' => "(".__('partner:re_request').")",
            'is_action' => true,
            'href' => "{$org->getURL()}/request_partner"
        ));
    }
    else
    {
        echo __('partner:exists');
    }
?>
</td>
<td class='commBoxMain' style='border-left:1px solid gray'>
<a href='<?php echo $org->getURL() ?>/compose'><?php echo __('message:link'); ?></a>
</td>
<td class='commBoxRight'>
&nbsp;
</td>
</table>
<?php
}
?>