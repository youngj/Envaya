<?php
    $org = $vars['org'];
?>
<div class='section_content padded'>    
    <form method='POST' action='<?php echo $org->get_url() ?>/reporting/new'>
    <?php echo view('input/securitytoken'); ?>
    <div class='input'>
        <label>Form Template</label><br />
    <?php    
        echo view('input/pulldown', array('name' => 'handler_class', 
            'options' => ReportDefinition::get_handler_options(),
        ));
    ?>  
    </div>

    <?php
        echo view('input/submit', array('value' => __('save')));
    ?>
    
    </form>
</div>   
