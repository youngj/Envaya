<div class='section_content padded'>
<?php
    ob_start();
    echo "<label>" . __('username') . "</label>";
    
    echo "<p>".view('input/text', array(
            'name' => 'username',
            'style' => 'width:200px',
            'value' => $vars['username']
    ))."</p>";
            
    echo "<div class='help'>".__('login:resetreq:help')."</div>";
        
    echo "<p>" . view('input/submit', array('value' => __('login:resetreq:submit'))) . "</p>";
    $form_body = ob_get_clean();
    
    echo view('focus', array('name' => 'username'));
    echo view('input/form', array('action' => "/pg/forgot_password", 'body' => $form_body)); 
?>
</div>