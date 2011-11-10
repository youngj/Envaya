<div class='padded'>
<div id="logbrowser_search_area">
<?php

    $user = $vars['user'];
    $timelower = $vars['timelower'];
    $timeupper = $vars['timeupper'];
   
    ob_start();
    echo "<p>";
    echo __('logbrowser:user');
    echo view('input/text',array(
        'name' => 'search_username',
        'value' => $user ? $user->username : '', 
    ));
    echo "</p>";

    echo "<p>";
    echo  __('logbrowser:starttime');
    echo view('input/text',array(
        'name' => 'timelower',
        'value' => $timelower
    ));
    echo "</p>";

    echo "<p>";
    echo __('logbrowser:endtime');    
    echo  view('input/text',array(
        'name' => 'timeupper',
        'value' => $timeupper
    ));
    echo "</p>";
    
    echo  view('input/submit',array(
        'value' => __('search')
    ));
                  
    $form_body = ob_get_clean();
    							
?>
    <div id="logbrowserSearchform"><?php        
        echo view('input/form',array(
            'body' => $form_body,
            'method' => 'GET',
            'action' => "/admin/logbrowser"
        ));
    ?></div>
</div>
</div>
<?php
    echo view('pagination', $vars);
?>
<table class="log_entry">
<?php
    foreach ($vars['entries'] as $entry)
    {
        echo view("admin/log_entry", array('entry' => $entry));
    }
?>
</table>
