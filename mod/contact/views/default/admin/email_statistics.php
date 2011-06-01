<?php
    $email = $vars['email'];
    
    $totalQueued = $email->query_outgoing_mail()->where('status = ?', OutgoingMail::Queued)->count(); 
    $totalSent = $email->query_outgoing_mail()->where('status = ?', OutgoingMail::Sent)->count(); 
    $totalFailed = $email->query_outgoing_mail()->where('status = ?', OutgoingMail::Failed)->count(); 
  
    if ($totalQueued || $totalSent || $totalFailed) {
?>
<div style='float:right'>
<h3>Statistics</h3>
<ul>
<?php
    if ($totalSent > 0)
    {
        echo "<li>Emails sent successfully: $totalSent</li>";        
    }
      
    if ($totalQueued > 0) 
    {
        echo "<li>Emails waiting to be sent: $totalQueued</li>";
    }
    
    if ($totalFailed > 0)
    {
        echo "<li>Emails failed to send: $totalFailed</li>";        
    }

    if ($totalSent > 0)
    {
        $lastTimeSent = $email->query_outgoing_mail()->where('status = ?', OutgoingMail::Sent)
            ->order_by('time_sent desc')
            ->columns('time_sent')
            ->get()
            ->time_sent; 
        echo "<li>Last email sent: ".friendly_time($lastTimeSent)."</li>";
    }    
?>
</ul>
</div>
<?php
    }
?>