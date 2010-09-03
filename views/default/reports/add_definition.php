<?php
    $org = $vars['org'];
?>
<div class='section_content padded'>    
    <form method='POST' action='<?php echo $org->get_url() ?>/reporting/new'>
    <?php echo view('input/securitytoken'); ?>
    <div class='input'>
        <label>Form Template</label><br />
    <?php    
        echo view('input/pulldown', array('internalname' => 'handler_class', 
            'options_values' => ReportDefinition::get_handler_options(),
        ));
    ?>  
    </div>

    <div class='input'>
        <label>Report Name</label><br />
    <?php    
        echo view('input/text', array('internalname' => 'report_name'));
    ?>  
    </div>    
    
    <?php
        echo view('input/submit', array('value' => __('save')));
    ?>
    
    </form>
</div>   
