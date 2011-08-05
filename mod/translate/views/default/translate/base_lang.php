<?php
    $key = $vars['key'];    
    $entity = $key->get_container_entity();
?>

<form method='POST' action='<?php echo escape(Request::get_uri()); ?>'>
<?php echo view('input/securitytoken'); ?>
<div class='input'>
<label>
<?php
    echo __('itrans:original_text');
?>
</label><br />
<?php
    echo $key->view_value($key->get_current_base_value(), 300);
?>
</div>

<div class='input'>
<label>
<?php
    echo __('itrans:select_base_lang');
?>
</label><br />
<?php
    echo view('input/pulldown', array(
        'name' => 'base_lang', 
        'empty_option' => __('itrans:unknown'),
        'value' => $entity->language,
        'options' => Language::get_options()
    ));    
?>
</div>
<?php
    echo view('input/submit', array('value' => __('savechanges')));
?>
</form>