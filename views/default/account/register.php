<?php     
    $next = Input::get_string('next');
?>
<div class='section_content padded' style='padding-top:0px'>
<form method='POST' action='/pg/register'>
<?php 
    echo view('input/securitytoken'); 
    echo view('input/hidden', array('name' => 'next', 'value' => $next)); 
    echo view('account/register_content', array('next' => $next)); 
?>
<div class='input'>
<label><?php echo __('register:click_to_create') ?></label>
<br />
<?php echo view('input/submit',array(
    'value' => __('register:create_button'),
    'track_dirty' => true
));
?>
</div>
</form>
</div>