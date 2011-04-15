<?php

    $widget = $vars['widget'];
    $org = $widget->get_container_entity();

    $form = view('news/add_post', array('org' => $org));

    echo view('section', array('header' => __("dashboard:add_update"), 'content' => $form));

    $offset = (int) get_input('offset');
    $limit = 10;

    $count = $org->query_news_updates()->count();
    $updates = $org->query_news_updates()->limit($limit, $offset)->filter();

    ob_start();

    if ($count)
    {

        echo  view('pagination',array(
            'baseurl' => $widget->get_edit_url(),
            'offset' => $offset,
            'count' => $count,
            'limit' => $limit,
        ));

        $escUrl = urlencode($_SERVER['REQUEST_URI']);
        echo "<table class='gridTable'>";

        $count = 0;

?>
<tr class='header_row'>
    <th colspan='3'><?php echo __("widget:news:item"); ?></th>
    <th><div class='header_icons edit_icon'></div></th>
    <th><div class='header_icons delete_icon'></div></th>
</tr>

<?php

        foreach ($updates as $update)
        {
            $count += 1;
            $rowClass = (($count % 2) != 0) ? 'odd' : 'even';

            echo "<tr class='$rowClass'>";
            echo "<td>". view('output/text', array('value' => $update->get_snippet()))."</td>";
            echo "<td><span class='blog_date'>{$update->get_date_text()}</span></td>";
            echo "<td><a href='{$update->get_url()}'>".__("view")."</a></td>";

            echo "<td><a href='{$update->get_url()}/edit?from=$escUrl'>".__("edit")."</a></td>";
            echo "<td>".view('output/confirmlink', array(
                'href' => "{$update->get_url()}/edit?delete=1",
                'confirm' => __('blog:delete:confirm'),
                'text' => __('delete')
            ))."</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    else
    {
        echo "<div>".__("widget:news:empty")."</div>";
    }

    $content = ob_get_clean();
    
    $form = view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content,
        'noSave' => true,
    ));    

    echo view("section", array(
        'header' => __("widget:news:manage_updates"),
        'content' => $form
    ));
?>
