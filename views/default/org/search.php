<?php
    $query = $vars['query'];
    $sector = $vars['sector'];

?>
<div class='padded'>
<div class='instructions'>
    <?php echo __('search:instructions'); ?>
</div>    

<form method='GET' class='searchForm' action='/org/search/'>    
    <?php echo view('input/text', array('internalname' => 'q', 'class' => 'searchField input-text', 'value' => $query)); ?>
    <?php echo view('input/pulldown', array('internalname' => 'sector',
        'options' => Organization::get_sector_options(), 
        'empty_option' => __('sector:empty_option'),
        'value' => $vars['sector'])) 
    ?>
    <br />
    
    <?php 
        echo view('input/submit', array(
            'internalname' => 'submit',
            'value' => __('search:submit') 
        ));
    ?>    
    
</form>
</div>