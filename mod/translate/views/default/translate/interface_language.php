<?php
    $language = $vars['language'];
    $groups = $language->query_groups()->order_by('name')->filter();
?>
<table class='gridTable' style='width:300px'>
<?php
    echo "<tr>";
    echo "<th>".__('itrans:language_group')."</th>";
    echo "<th>".__('itrans:progress')."</th>";
    echo "</tr>";
    
    $available_keys = 0;
    $lang_keys = 0;

    foreach ($groups as $group)
    {
        $group_available_keys = sizeof($group->get_defined_default_group());
        $available_keys += $group_available_keys;
        $lang_keys += $group->num_keys;
    
        echo "<tr>";
        echo "<td style='font-weight:bold'><a href='{$group->get_url()}'>".escape($group->name)."</a></td>";
        echo "<td>{$group->num_keys} / {$group_available_keys}</td>";
        echo "</tr>";
    }
    
   echo "<tr>";
    echo "<td style='font-weight:bold'>".__('total')."</td>";
    echo "<td>{$lang_keys} / {$available_keys}</td>";
    echo "</tr>";
?>
</table>