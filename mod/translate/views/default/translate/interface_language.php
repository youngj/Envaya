<?php
    $language = $vars['language'];
    $groups = $language->query_groups()
        ->order_by('name')->filter();
?>
<div style='float:left;width:400px;margin-right:20px'>
<table class='gridTable'>
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
</div>
<div style='float:left'>
<ul style='font-weight:bold'>
<?php 
    $user = Session::get_loggedin_user();
    if ($user)
    {
        $stats = $language->get_stats_for_user($user);
        if ($stats->guid)
        {
            echo "<li><a href='{$stats->get_url()}'>".__('itrans:yours')."</a></li>";         
        }
    }

    echo "<li><a href='{$language->get_url()}/latest'>".__('itrans:latest')."</a></li>"; 
    echo "<li><a href='{$language->get_url()}/comments'>".__('itrans:latest_comments')."</a></li>"; 
    echo "<li><a href='{$language->get_url()}/translators'>".__('itrans:translators')."</a></li>";     
    
    if (Session::isadminloggedin())
    {
        echo "<li><a style='color:red' href='{$language->get_admin_url()}'>".__('admin')."</a></li>";         
    }
    
    echo "<li><a href='/tr/instructions#target_language' target='_blank'>".__('itrans:instructions')."</a></li>";     
    
?>
</ul>
</div>