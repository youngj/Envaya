
<?php

    $sort = sanitize_order_by(get_input('sort') ?: 'name');
    $baseurl = "admin/contact?sort=$sort";
    $offset = (int)get_input('offset');

    $limit = 15;
    $orgs = Organization::query()->order_by($sort)->limit($limit, $offset)->filter();
    $count = Organization::query()->count();

    echo elgg_view('navigation/pagination',array(
        'baseurl' => $baseurl,
        'pagesShown' => 24,
        'offset' => $offset,
        'count' => $count,
        'limit' => $limit
    ));
?>


<table class='gridTable'>
<tr>
    <th><a href='admin/contact?sort=name'><?php echo __('name') ?></a></th>
    <th><a href='admin/contact?sort=email'><?php echo __('email') ?></a></th>
    <th><?php echo __('user:phone:label') ?></th>
    <th><a href='admin/contact?sort=time_created'><?php echo __('created') ?></a></th>
    <th><?php echo __('last_update') ?></th>
    <th><?php echo __('posts') ?></th>
    <th><?php echo __('lang') ?></th>
    <th><a href='admin/contact?sort=last_notify_time'><?php echo __('last_notify') ?></a></th>
</tr>
<?php
    $escUrl = urlencode($_SERVER['REQUEST_URI']);

    foreach ($orgs as $org)
    {
        $backgroundColor = ($org->approval > 0) ? '#fff' : (($org->approval == 0) ? '#ddd' : '#f99');
        $numNewsUpdates = $org->queryNewsUpdates()->count();
?>
<tr style='background-color:<?php echo $backgroundColor ?>'>
    <td><?php echo "<a href='{$org->getURL()}'>".escape($org->name)."</a>" ?></td>
    <td><?php echo "<a href='mailto:".escape($org->email)."'>".escape($org->email)."</a>" ?></td>
    <td><?php echo escape($org->phone_number) ?></td>
    <td><?php echo friendly_time($org->time_created); ?></td>
    <td><?php
        $feedItems = $org->getFeedItems(1);
        if (sizeof($feedItems) > 0)
        {
            echo friendly_time($feedItems[0]->time_posted);
        }
    ?></td>
    <td><?php echo $numNewsUpdates; ?></td>
    <td><?php echo $org->language; ?></td>
    <td><?php echo $org->last_notify_time ? friendly_time($org->last_notify_time) : ''; ?></td>
    <td style='padding:0px;background-color:#068488;white-space:nowrap'>
        <?php echo "<a href='{$org->username}/settings?from=$escUrl'><img src='_graphics/settings.gif'></a>" ?>
        <a href='admin/confirm_email?username=<?php echo $org->username ?>&from=<?php echo $escUrl ?>'><img src='_graphics/message.gif' style='vertical-align:5px'></a>
    </td>
</tr>
<?php } ?>
</table>
