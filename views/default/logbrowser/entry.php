<?php

	$entry = $vars['entry'];
	
	$by = get_entity($entry->performed_by_guid);
	$object = SystemLog::get_loggable_object($entry);
	
	$obj_url = is_callable(array($object, 'get_url')) ? $object->get_url() : '';
	
?>	
    <tr>
    
        <td class="log_entry_time">
            <?php 
                echo date('r', $entry->time_created ); 
            ?>
        </td>
        <td class="log_entry_user">
        <?php
            if ($by) {
                echo "<a href=\"".$by->get_url()."\">".escape($by->name)."</a>";
                echo " <a href=\"?user_guid={$by->guid}\">" . $by->guid . "</a>"; 
            } 
            else 
                echo "&nbsp;";             
        ?>
        <td>
        <td class="log_entry_item">
        <?php 
                if ($obj_url) echo "<a href=\"$obj_url\">";
                echo "{$entry->object_class}";
                if ($obj_url) echo "</a>";
                echo " " . $entry->object_id;
        ?>
        </td>
        <td class="log_entry_action">            
            <?php  echo __($entry->event); ?>            
        </td>
    </tr>
	