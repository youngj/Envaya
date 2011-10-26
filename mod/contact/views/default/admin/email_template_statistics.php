<?php
    $template = $vars['template'];
    
    $totalQueued = $template->query_outgoing_messages()->where('status = ?', OutgoingMail::Queued)->count(); 
    $totalSent = $template->query_outgoing_messages()->where('status = ?', OutgoingMail::Sent)->count(); 
    $totalBounced = $template->query_outgoing_messages()->where('status = ?', OutgoingMail::Bounced)->count(); 
    $totalFailed = $template->query_outgoing_messages()->where('status = ?', OutgoingMail::Failed)->count(); 
  
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

    if ($totalBounced > 0)
    {
        echo "<li>Emails bounced: $totalBounced</li>";        
    }
    
    
    if ($totalSent > 0)
    {
        $lastTimeSent = $template->query_outgoing_messages()->where('status = ?', OutgoingMail::Sent)
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