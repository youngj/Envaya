<div id='heading'>
<h1>
<?php

    $items = array(
        array('url' => '/admin/contact/email', 'title' => __('email:list'))
    );
    
    $email = @$vars['email'];
    if ($email)
    {
        $items[] = array(
            'url' => $email->get_url(), 
            'title' => $email->subject
        );
    }
    
    $title = @$vars['title'];
    if ($title)
    {
        $items[] = array('title' => $title);
    }
    
    echo view('breadcrumb', array('items' => $items));
?>
</h1>
</div>