<?php

    $sort = Database::sanitize_order_by(get_input('sort') ?: 'name');
    $offset = (int)get_input('offset');

    $limit = 15;
    
    $filters = Query_Filter::filters_from_input(array(
        'Query_Filter_User_Fulltext',
        'Query_Filter_User_Type',
        'Query_Filter_User_Sector',
        'Query_Filter_User_Country',
        'Query_Filter_User_Region',
        'Query_Filter_User_Approval',
    ));
            
    $query = User::query()
        ->apply_filters($filters)
        ->order_by($sort);
        
    $cur_url = $_SERVER['REQUEST_URI'];
       
    $orgs = $query->limit($limit, $offset)->filter();
    $count = $query->count();
    
    echo "<div style='float:right;padding:4px;border:1px solid #ccc'>$count users in filter</div>";    
    
    echo view('org/filter_controls', array(
        'baseurl' => "/admin/contact?sort=$sort",
        'filters' => $filters
    ));
       
    echo view('pagination',array(
        'pagesShown' => 24,
        'offset' => $offset,
        'count' => $count,
        'limit' => $limit
    ));
?>
<table class='gridTable'>
<tr>
    <th><a href='<?php echo escape(url_with_param($cur_url,'sort','name')); ?>'><?php echo __('name') ?></a></th>
    <th><a href='<?php echo escape(url_with_param($cur_url,'sort','email')); ?>'><?php echo __('email') ?></a></th>
    <th><?php echo __('phone_number') ?></th>
    <th><a href='<?php echo escape(url_with_param($cur_url,'sort','time_created')); ?>'><?php echo __('contact:time_created') ?></a></th>
    <th><a href='<?php echo escape(url_with_param($cur_url,'sort','last_action')); ?>'><?php echo __('contact:last_action') ?></a></th>
    <th><?php echo __('contact:num_pages') ?></th>
    <th><?php echo __('language') ?></th>
</tr>
<?php
    $escUrl = urlencode($_SERVER['REQUEST_URI']);

    foreach ($orgs as $org)
    {
        $backgroundColor = ($org->approval > 0) ? '#fff' : (($org->approval == 0) ? '#ddd' : '#f99');
        $numWidgets = $org->query_widgets()->count();
?>
<tr style='background-color:<?php echo $backgroundColor ?>'>
    <td><?php echo "<a href='{$org->get_url()}'>".escape($org->name)."</a>" ?></td>
    <td><?php echo "<a href='mailto:".escape($org->email)."'>".escape($org->email)."</a>" ?></td>
    <td><?php echo escape($org->phone_number) ?></td>
    <td><?php echo friendly_time($org->time_created); ?></td>
    <td><?php echo friendly_time($org->last_action); ?></td>
    <td><?php echo $numWidgets; ?></td>
    <td><?php echo $org->language; ?></td>
    <td style='padding:0px;background-color:#068488;white-space:nowrap'>
        <?php 
            echo "<a href='/{$org->username}/settings?from=$escUrl'><img src='/_media/images/settings.gif'></a>";
            
            foreach (EmailSubscription_Contact::query_for_entity($org)->filter() as $subscription)
            {            
                echo "<a href='/admin/contact/email/subscription/{$subscription->guid}?from={$escUrl}'><img src='/_media/images/message.gif' style='vertical-align:5px'></a>";
            }
            
            foreach (SMSSubscription_Contact::query_for_entity($org)->filter() as $subscription)
            {            
                echo "<a href='/admin/contact/sms/subscription/{$subscription->guid}?from={$escUrl}'><img src='/_media/images/phone.gif' style='padding-left:2px;vertical-align:3px'></a>";
            }            
        ?>
    </td>
</tr>
<?php } ?>
</table>
