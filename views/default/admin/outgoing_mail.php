<div class='padded'>
<?php
    $query = OutgoingMail::query()->order_by('id desc');
    
    $offset = get_input('offset');
    $limit = 20;
    
    $mails = $query->limit($limit, $offset)->filter();
    
    $vars = array(
        'count' => null,
        'count_displayed' => sizeof($mails),
        'offset' => $offset,
        'limit' => $limit,
    );
        
    echo view('pagination', $vars);
    echo "<table class='gridTable'>";
        echo "<tr>";
        echo "<th>To</th>";
        echo "<th>Subject</th>";
        echo "<th>Time Queued</th>";
        echo "<th>Time Sent</th>";
        echo "<th>Status</th>";        
        echo "<th>&nbsp;</th>";
        echo "</tr>";   
        
    foreach ($mails as $mail)
    {
        echo "<tr>";
        echo "<td>".escape($mail->to_address)."</td>";
        echo "<td>".escape($mail->subject)."</td>";
        echo "<td style='white-space:nowrap'>".friendly_time($mail->time_queued)."</td>";
        echo "<td style='white-space:nowrap'>".friendly_time($mail->time_sent)."</td>";
        echo "<td>".$mail->get_status_text()."</td>";     

        echo "<td>";        
        echo "<a href='/admin/view_mail?id={$mail->id}'>".__('view')."</a>";      
        echo " &middot; ";                
        echo view('admin/mail_actions', array('mail' => $mail));
        echo "</td>";
        echo "</tr>";
    }    
    echo "</table>";
?>

</div>