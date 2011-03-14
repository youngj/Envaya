<?php
    $query = $vars['query'];
    $sector = $vars['sector'];
?>
<div class='padded'>
<div class='instructions'>
    <?php echo __('search:instructions'); ?>
</div>    

<form method='GET' class='searchForm' action='/org/search/'>    
    <?php echo view('input/text', array('name' => 'q', 'class' => 'searchField input-text', 'value' => $query)); ?>
    <?php echo view('input/pulldown', array('name' => 'sector',
        'options' => Organization::get_sector_options(), 
        'empty_option' => __('sector:empty_option'),
        'value' => $vars['sector'])) 
    ?>
    <br />
    
    <?php 
        echo view('input/submit', array(
            'name' => 'submit',
            'value' => __('search:submit') 
        ));
    ?>        
</form>
<?php echo view('focus', array('name' => 'q')); ?>
</div>