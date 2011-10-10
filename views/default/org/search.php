<?php
    $query = $vars['query'];
    $filters = $vars['filters'];
?>
<div class='section_content padded'>
<div class='instructions'>
    <?php echo __('search:instructions'); ?>
</div>    
<?php
    echo view('org/search_form', array(
        'query' => $query,
        'filters' => $filters,
    ));
    echo view('focus', array('name' => 'q')); 
 ?>
</div>