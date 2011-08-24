<?php
    $query = $vars['query'];
    $filters = $vars['filters'];
?>
<form method='GET' class='searchForm' action='/pg/search/'>    
    <?php echo view('input/text', array('name' => 'q', 'class' => 'searchField input-text', 'value' => $query)); ?>
    <?php     
        foreach ($filters as $filter)
        {
            echo $filter->render_input(array(
                'name' => $filter->get_param_name(),                
            ))." ";
        }           
    ?>
    <br />
    
    <?php 
        echo view('input/submit', array(
            'value' => __('search:submit') 
        ));
    ?>    
    
</form>
