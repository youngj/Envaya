<div class='padded'>
<div id="logbrowser_search_area">
    <div class='thin_column' id="logbrowserSearchform">
    <form method='GET' action='/admin/logbrowser'>
<?php

    $user = $vars['user'];
    $timelower = $vars['timelower'];
    $timeupper = $vars['timeupper'];
   
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
                                
?>    
    </form>
</div>
</div>
</div>
<?php
    echo view('pagination', $vars);
?>
<table class="gridTable">
<?php
    $entries = $vars['entries'];
    if ($entries)
    {
        foreach ($entries as $entry)
        {
            echo view("admin/log_entry", array('entry' => $entry));
        }
    }
    else
    {
        echo "No log entries found.";
    }
?>
</table>
