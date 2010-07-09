
<?php

    $sort = sanitize_order_by(get_input('sort') ?: 'name');
    $baseurl = "org/contact?sort=$sort";
    $offset = (int)get_input('offset');

    $limit = 15;
    $orgs = Organization::all($sort, $limit, $offset);
    $count = Organization::all('', 0, 0, true);

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
    <th><a href='org/contact?sort=name'><?php echo elgg_echo('name') ?></a></th>
    <th><a href='org/contact?sort=email'><?php echo elgg_echo('email') ?></a></th>
    <th><?php echo elgg_echo('user:phone:label') ?></th>
    <th><?php echo elgg_echo('last_update') ?></th>
    <th><?php echo elgg_echo('num_posts') ?></th>
</tr>
<?php
    $escUrl = urlencode($_SERVER['REQUEST_URI']);

    foreach ($orgs as $org)
    {
        $backgroundColor = ($org->approval > 0) ? '#fff' : (($org->approval == 0) ? '#ddd' : '#f99');
        $updates = $org->getNewsUpdates(1);
        $numNewsUpdates = $org->getNewsUpdates(0,0,true);
?>
<tr style='background-color:<?php echo $backgroundColor ?>'>
    <td><?php echo "<a href='{$org->getURL()}'>".escape($org->name)."</a>" ?></td>
    <td><?php echo "<a href='mailto:".escape($org->email)."'>".escape($org->email)."</a>" ?></td>
    <td><?php echo escape($org->phone_number) ?></td>
    <td><?php
        if (sizeof($updates) > 0)
        {
            echo friendly_time($updates[0]->time_created);
        }
    ?></td>
    <td><?php
        echo $numNewsUpdates;
    ?></td>
    <td style='padding:0px;background-color:#068488'><?php echo "<a href='pg/settings/user/{$org->username}?from=$escUrl'><img src='_graphics/settings.gif'></a>" ?></td>
</tr>
<?php } ?>
</table>
