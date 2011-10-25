<div class='padded'>
<?php
    $sms = $vars['sms'];
    
    echo "<table class='gridTable'>";

    echo "<tr>";
    echo "<th>From</th>";
    echo "<td style='white-space:nowrap'>".escape($sms->from_number)."</td>";
    echo "</tr>";          

    echo "<tr>";
    echo "<th>To</th>";
    echo "<td style='white-space:nowrap'>".escape($sms->to_number)."</td>";
    echo "</tr>";          

    echo "<tr>";
    echo "<th>Message</th>"; 
    echo "<td>".nl2br(escape($sms->message))."</td>";    
    echo "</tr>";        
        
    echo "<tr>";
    echo "<th>Time Created</th>";
    echo "<td style='white-space:nowrap'>".friendly_time($sms->time_created)."</td>";
    echo "</tr>";          
    
    echo "<tr>";
    echo "<th>Time Sent</th>";
    echo "<td style='white-space:nowrap'>".friendly_time($sms->time_sent)."</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th>Status</th>"; 
    echo "<td>".$sms->get_status_text();
    echo $sms->error_message ? (": ".escape($sms->error_message)) : "";
    echo "</td>";    
    echo "</tr>"; 
    
    echo "<tr>";
    echo "<th>Message Type</th>";
    echo "<td style='white-space:nowrap'>".$sms->get_message_type_text()."</td>";
    echo "</tr>";        
    
    echo "<tr>";
    echo "<th>Provider</th>";
    echo "<td style='white-space:nowrap'>".escape($sms->get_provider()->get_subclass_name())."</td>";
    echo "</tr>";         
    
    
    echo "</table>";
    
    echo "<div style='font-weight:bold;font-size:14px;text-align:center'>";
    echo view('admin/sms_actions', array('sms' => $sms));    
    echo "</div>";

?>

</div>