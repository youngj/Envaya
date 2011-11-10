<?php
	$entry = $vars['entry'];
	
	$user = $entry->get_user_entity();
	$object = $entry->get_model_object();
	
    $obj_url = ($object && is_callable(array($object, 'get_url'))) ? $object->get_url() : '';	
?>	
    <tr>    
        <td class="log_entry_time">
            <?php 
                echo date('r', $entry->time_created); 
            ?>
        </td>
        <td class="log_entry_user">
        <?php
            if ($user) 
            {
                echo "<a href=\"".$user->get_url()."\">".escape($user->name)."</a>";
            } 
            else 
                echo "&nbsp;";             
        ?>
        <td>
        <td class="log_entry_item">
        <?php 
            if ($obj_url) echo "<a href=\"$obj_url\">";
            echo get_class($entry->get_model_object());
            echo " ({$entry->object_id})";
            if ($obj_url) echo "</a>";
        ?>
        </td>
        <td class="log_entry_action">            
            <?php echo escape(__($entry->event_name)); ?>
        </td>
    </tr>