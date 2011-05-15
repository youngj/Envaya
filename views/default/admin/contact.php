<?php

    $sort = Database::sanitize_order_by(get_input('sort') ?: 'name');
    $baseurl = "/admin/contact?sort=$sort";
    $offset = (int)get_input('offset');

    $limit = 15;
    
    $sector = get_input('sector');
    $region = get_input('region');
    
    $query = Organization::query()->order_by($sort);
    
    if ($sector)
    {
        $query->with_sector($sector);
    }       
    if ($region)
    {
        $query->with_region($region);
    }
    
    $orgs = $query->limit($limit, $offset)->filter();
    $count = $query->count();

    echo view('org/filter_controls', array('baseurl' => $baseurl));
    
    echo view('pagination',array(
        'baseurl' => $baseurl,
        'pagesShown' => 24,
        'offset' => $offset,
        'count' => $count,
        'limit' => $limit
    ));
?>
<table class='gridTable'>
<tr>
    <th><a href='/admin/contact?sort=name'><?php echo __('name') ?></a></th>
    <th><a href='/admin/contact?sort=email'><?php echo __('email') ?></a></th>
    <th><?php echo __('phone_number') ?></th>
    <th><a href='/admin/contact?sort=time_created'><?php echo __('user:time_created') ?></a></th>
    <th><?php echo __('user:time_updated') ?></th>
    <th><?php echo __('user:num_pages') ?></th>
    <th><?php echo __('language') ?></th>
    <th><a href='/admin/contact?sort=last_notify_time'><?php echo __('user:last_notify') ?></a></th>
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
    <td><?php
        $feedItem = $org->query_feed_items()->limit(1)->get();
        if ($feedItem)
        {
            echo friendly_time($feedItem->time_posted);
        }
    ?></td>
    <td><?php echo $numWidgets; ?></td>
    <td><?php echo $org->language; ?></td>
    <td><?php echo $org->last_notify_time ? friendly_time($org->last_notify_time) : ''; ?></td>
    <td style='padding:0px;background-color:#068488;white-space:nowrap'>
        <?php echo "<a href='/{$org->username}/settings?from=$escUrl'><img src='/_graphics/settings.gif'></a>" ?>
        <a href='/admin/send_email?orgs[]=<?php echo $org->guid ?>&from=<?php echo $escUrl ?>'><img src='/_graphics/message.gif' style='vertical-align:5px'></a>
    </td>
</tr>
<?php } ?>
</table>
