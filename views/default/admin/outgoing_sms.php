<div class='padded'>
<?php
    $query = OutgoingSMS::query()->order_by('id desc');
    
    $offset = Input::get_string('offset');
    $limit = 20;
    
    $messages = $query->limit($limit, $offset)->filter();
    
    $vars = array(
        'count' => null,
        'count_displayed' => sizeof($messages),
        'offset' => $offset,
        'limit' => $limit,
    );
        
    echo view('pagination', $vars);
    echo "<table class='gridTable'>";
        echo "<tr>";
        echo "<th>From</th>";
        echo "<th>To</th>";
        echo "<th>Message</th>";
        echo "<th>Time Created</th>";
        echo "<th>Time Sent</th>";
        echo "<th>Status</th>";        
        echo "<th>&nbsp;</th>";
        echo "</tr>";   
        
    foreach ($messages as $sms)
    {
        echo "<tr>";
        echo "<td>".escape($sms->from_number)."</td>";
        echo "<td>".escape($sms->to_number)."</td>";
        echo "<td>".nl2br(escape($sms->message))."</td>";             
        echo "<td style='white-space:nowrap'>".friendly_time($sms->time_created)."</td>";
        echo "<td style='white-space:nowrap'>".friendly_time($sms->time_sent)."</td>";
        echo "<td>".$sms->get_status_text();
        echo $sms->error_message ? (": ".escape($sms->error_message)) : "";
        "</td>";     

        echo "<td>";        
        echo "<a href='/admin/view_sms?id={$sms->id}'>".__('view')."</a>";      
        echo " &middot; ";
        echo view('admin/sms_actions', array('sms' => $sms));
        echo "</td>";
        echo "</tr>";
    }    
    echo "</table>";
?>
</div>