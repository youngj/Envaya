<?php
    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();

?>

<div class='section_header'><?php echo elgg_echo('widget:team:add'); ?></div>
<div class='section_content padded'>

<form action='action/org/addTeamMember' enctype='multipart/form-data' method='POST'>

<?php echo elgg_view('input/securitytoken'); ?>

<div class='input'>
<label><?php echo elgg_echo('widget:team:name'); ?></label>
<?php
    echo elgg_view('input/text', 
        array(
            'internalname' => 'name', 
            'trackDirty' => true,
        )
    );
?>
</div>

<div class='input'>
<label><?php echo elgg_echo('widget:team:description'); ?></label>
<?php
    echo elgg_view('input/longtext', 
        array(
            'internalname' => 'description', 
            'trackDirty' => true,
            'js' => "style='height:60px'",            
        )
    );
?>    
</div>

<div class='input'>
<label><?php echo elgg_echo('widget:team:photo'); ?></label><br />

<?php echo elgg_view('input/swfupload_image', array(
    'internalname' => 'image',
    'trackDirty' => true,
    'sizes' => TeamMember::getImageSizes()
)) ?>

</div>

<?php
    echo elgg_view('input/hidden', 
        array('internalname' => 'org_guid', 
            'value' => $org->guid)); 

    echo elgg_view('input/submit', 
        array('internalname' => 'submit', 
            'class' => "submit_button",
            'trackDirty' => true,
            'value' => elgg_echo('widget:team:add:button'))); 

?>

</form>

</div>

<div class='section_header'><?php echo elgg_echo('widget:team:list'); ?></div>
<div class='section_content padded'>
<?php
    $teamMembers = $org->getTeamMembers();
    
    if (!empty($teamMembers))
    {    
        $escUrl = urlencode($_SERVER['REQUEST_URI']);
        echo "<table class='gridTable'>";    
        
        $count = sizeof($teamMembers);
        $i = 0;

?>
<tr class='header_row'>
    <th><?php echo elgg_echo("widget:team:member"); ?></th>
    <?php if ($count > 1) { ?>
        <th><div class='header_icons up_icon'></div></th>
        <th><div class='header_icons down_icon'></div></th>
    <?php } ?>
    <th><div class='header_icons edit_icon'></div></th>
    <th><div class='header_icons delete_icon'></div></th>
</tr>
<?php        
        
        foreach ($teamMembers as $teamMember)
        {        
            $rowClass = (($i % 2) != 0) ? 'odd' : 'even';
        
            echo "<tr class='$rowClass'>";
            echo "<td>".escape($teamMember->name)."</td>";                        

            if ($count > 1)
            {
                if ($i > 0)
                {
                    echo "<td>".elgg_view('output/link', array( 
                        'is_action' => true,
                        'href' => "action/org/moveTeamMember?member={$teamMember->guid}&delta=-1",
                        'text' => elgg_echo('move:up'),
                    ))."</td>";
                }   
                else
                {
                    echo "<td>&nbsp;</td>";
                }

                if ($i < $count - 1)
                {
                    echo "<td>".elgg_view('output/link', array( 
                        'is_action' => true,
                        'href' => "action/org/moveTeamMember?member={$teamMember->guid}&delta=1",
                        'text' => elgg_echo('move:down'),
                    ))."</td>";  
                }   
                else
                {
                    echo "<td>&nbsp;</td>";
                }
            }
            echo "<td><a href='{$teamMember->getEditURL()}?from=$escUrl'>".elgg_echo("edit")."</a></td>";
            echo "<td>".elgg_view('output/confirmlink', array(
                'is_action' => true,
                'href' => "action/org/deleteTeamMember?member={$teamMember->guid}",
                'text' => elgg_echo('delete')
            ))."</td>";
            echo "</tr>";
            
            $i += 1;
        }   
        echo "</table>";
    }
    else
    {
        echo elgg_echo("widget:team:empty");
    }


    echo elgg_view("widgets/edit_form", array(
        'widget' => $widget,
        'noSave' => true,
        'body' => ''
    ));
?>    

</div>