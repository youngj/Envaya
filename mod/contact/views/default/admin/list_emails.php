<div class='padded'>
<?php   
    $query = EmailTemplate::query()->order_by('time_created desc');
    $limit = 10;
    $offset = (int)get_input('offset');
    
    $emails = $query->limit($limit, $offset)->filter();
    $count = $query->count();
    
    $elements = array();
    
    foreach ($emails as $email)
    {
        ob_start();
        
        echo "<div class='email_item' style='padding:3px'>";
        
        echo "<div style='float:right'>";
        
        echo "<a href='{$email->get_url()}/edit'>".__('edit')."</a> &middot; ";
        echo "<a href='{$email->get_url()}/send'>".__('email:send')."</a>";

        echo "</div>";
        
        echo "<a href='{$email->get_url()}'><strong>".escape($email->subject ?: '(No Subject)')."</strong></a>";
        echo " (".get_date_text($email->time_created).")";
        
        echo "</div>";
        
        $elements[] = ob_get_clean();
    }
   
    echo view('paged_list', array(
        'elements' => $elements,
        'count' => $count,
        'offset' => $offset,
        'limit' => $limit,
        'separator' => "<div class='separator'></div>"
    ));
?>
<br />
<a href='/admin/contact/email/add'>Add new email template</a>
</div>