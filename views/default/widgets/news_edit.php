<?php

    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();

    $form = elgg_view('org/addPost', array('org' => $org));

    echo elgg_view_layout('section', elgg_echo("dashboard:add_update"), $form);

    //ob_start();
?>
<!--
<p><?php echo elgg_echo('widget:news:mobile_summary') ?></p>

<ul>
<li><strong><?php echo elgg_echo('widget:news:email') ?></strong>:
<?php echo sprintf(elgg_echo('widget:news:email:summary'), "<strong>{$org->getPostEmail()}</strong>") ?>

<?php
    echo elgg_view('output/confirmlink', array(
        'text' => elgg_echo('widget:news:change_email'),
        'is_action' => true,
        'href' => "action/org/changeEmail?org_guid={$org->guid}"
    ));
?>
</li>
<li><strong><?php echo elgg_echo('widget:news:sms') ?></strong>:
<?php echo elgg_echo('widget:news:sms:summary') ?></li>
</ul>
-->
<?php
    //$settings = ob_get_clean();

    //echo elgg_view_layout('section', elgg_echo("widget:news:mobile_settings"), $settings);

    $offset = (int) get_input('offset');
    $limit = 10;

    $count = $org->getNewsUpdates($limit, $offset, true);
    $updates = $org->getNewsUpdates($limit, $offset);

    ob_start();

    if ($count)
    {

        echo  elgg_view('navigation/pagination',array(
            'baseurl' => $widget->getEditURL(),
            'offset' => $offset,
            'count' => $count,
            'limit' => $limit,
        ));

        $escUrl = urlencode($_SERVER['REQUEST_URI']);
        echo "<table class='gridTable'>";

        $count = 0;

?>
<tr class='header_row'>
    <th colspan='3'><?php echo elgg_echo("widget:news:item"); ?></th>
    <th><div class='header_icons edit_icon'></div></th>
    <th><div class='header_icons delete_icon'></div></th>
</tr>

<?php

        foreach ($updates as $update)
        {
            $count += 1;
            $rowClass = (($count % 2) != 0) ? 'odd' : 'even';

            echo "<tr class='$rowClass'>";
            echo "<td>". elgg_view('output/text', array('value' => $update->getSnippet()))."</td>";
            echo "<td><span class='blog_date'>{$update->getDateText()}</span></td>";
            echo "<td><a href='{$update->getURL()}'>".elgg_echo("view")."</a></td>";

            echo "<td><a href='{$update->getURL()}/edit?from=$escUrl'>".elgg_echo("edit")."</a></td>";
            echo "<td>".elgg_view('output/confirmlink', array(
                'is_action' => true,
                'href' => "{$update->getURL()}/save?delete=1",
                'confirm' => elgg_echo('blog:delete:confirm'),
                'text' => elgg_echo('delete')
            ))."</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    else
    {
        echo elgg_echo("widget:news:empty");
    }


    $content = ob_get_clean();

    echo elgg_view_layout('section', elgg_echo("widget:news:manage_updates"), $content);
?>
