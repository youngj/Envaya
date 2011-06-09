<?php
    $query = $vars['query'];
    $sector = $vars['sector'];
?>
<div class='padded'>
<div class='instructions'>
    <?php echo __('search:instructions'); ?>
</div>    

<form method='GET' class='searchForm' action='/pg/search/'>    
    <?php echo view('input/text', array('name' => 'q', 'class' => 'searchField input-text', 'value' => $query)); ?>
    <?php echo view('input/pulldown', array('name' => 'sector',
        'options' => OrgSectors::get_options(), 
        'empty_option' => __('sector:empty_option'),
        'value' => $vars['sector'])) 
    ?>
    <br />
    
    <?php 
        echo view('input/submit', array(
            'value' => __('search:submit') 
        ));
    ?>        
</form>
<?php echo view('focus', array('name' => 'q')); ?>
</div>