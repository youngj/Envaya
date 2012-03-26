<?php
    $entry = $vars['entry'];
    
    $user = $entry->get_user_entity();
    $subject = $entry->get_subject_entity();
    
    $subject_url = $subject ? $subject->get_url() : '';    
?>    
    <tr>    
        <td class="log_entry_time">
            <?php 
                echo date('m/d/y H:i:s', $entry->time_created); 
            ?>
        </td>
        <td class="log_entry_user">
        <?php
            if ($user) 
            {
                echo "<a href=\"".$user->get_url()."\">".escape($user->email)."</a>";
            } 
            else 
                echo "&nbsp;";             
        ?>
        <td class="log_entry_action">            
            <?php echo escape(__($entry->event_name)); ?>
        </td>        
        <td class="log_entry_item">
        <?php 
            if ($subject_url) echo "<a href=\"$subject_url\">";
            if ($subject)
            {
                echo escape($subject->get_title() ?: "(untitled)");
            }
            if ($subject_url) echo "</a>";
            if ($entry->message)
            {
                echo " ".escape($entry->message);
            }
        ?>
        </td>
        <td class="log_entry_action">            
            <?php echo escape($entry->ip_address . " ({$entry->get_source_text()})"); ?>
        </td>        
    </tr>