<div class='padded'>
<?php 
    $subscriptions = $vars['subscriptions'];
    $template = $vars['template'];    
?>

<form action='<?php echo $template->get_url() ?>/send' method='POST'>

<script type='text/javascript'>
function countRecipients()
{
    var form = document.forms[0];
    var checkboxes = form["subscriptions[]"];
    var count = 0;
    for (var i = 0; i < checkboxes.length; i++)
    {
        if (checkboxes[i].checked)
        {
            count++;
        }
    }
    $('recipient_count').innerHTML = count;
}
</script>

<?php echo view('input/securitytoken'); ?>
To: (<span id='recipient_count'><?php echo sizeof($subscriptions); ?></span> recipients)
<div style='<?php echo (sizeof($subscriptions) > 5) ? "height:150px;overflow:auto;" : ''; ?>font-size:10px'>
<?php
    
    $options = array();
    foreach ($subscriptions as $subscription)
    {
        $options[$subscription->guid] = $subscription->get_recipient_description();
    }

    echo view('input/checkboxes', array(
        'name' => 'subscriptions',
        'options' => $options,
        'value' => array_keys($options),
        'attrs' => array('onchange' => 'countRecipients()'),
    ));
?>
</div>
<br />

<?php 
    echo $vars['content'];
    
    echo view('input/hidden',array(
        'name' => 'from',
        'value' => get_input('from')
    ));    
    
    echo view('input/submit',array(
        'value' => __('message:send')
    ));

?>
</form>
</div>