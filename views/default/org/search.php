<?php
    $query = $vars['query'];
    $sector = $vars['sector'];

?>
<div class='instructions'>
    <?php echo __('search:instructions'); ?>
</div>    

<form method='GET' class='searchForm' action='/org/search/'>    
    <input class='searchField' type='text' name='q' value='<?php echo escape($query); ?>'>
    <?php echo view('input/pulldown', array('internalname' => 'sector',
        'options_values' => Organization::get_sector_options(), 
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
