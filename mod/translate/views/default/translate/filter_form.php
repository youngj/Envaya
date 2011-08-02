<?php
    $action = $vars['action'];
    $filter = $vars['filter'];

    $query = @$filter['q'];
    $status = @$filter['status'];
                                   
    echo "<form method='GET' id='filter_form' action='".escape($action)."'>";
    echo "<label>".__("itrans:filter")."</label> ";
    
    if (!@$vars['hide_query'])
    {
        echo view('input/text', array(
            'name' => 'q', 
            'style' => "width:150px;margin:0px",
            'attrs' => array(
                'onchange' => '$("filter_form").submit()'
            ),        
            'value' => $query
        ));
    }
    echo view('input/pulldown', array(
        'name' => 'status',
        'options' => array(
            '' => __('itrans:status_all'),
            'empty' => __('itrans:not_translated'),
            'translated' => __('itrans:translated'),
            'unapproved' => __('itrans:unapproved'),
            'approved' => __('itrans:approved'),
            
        ),
        'attrs' => array(
            'onchange' => '$("filter_form").submit()'
        ),
        'value' => $status,
    ));
        
    echo "</form>";
