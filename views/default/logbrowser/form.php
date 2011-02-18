
<div id="logbrowser_search_area">
<?php
	
    if ($vars['timelower']) {
        $lowerval = date('r',$vars['timelower']);
    } else {
        $lowerval = "";
    }
    if ($vars['timeupper']) {
        $upperval = date('r',$vars['timeupper']);
    } else {
        $upperval = "";
    }
    if ($vars['user_guid']) {
        if ($user = get_entity($vars['user_guid']))
            $userval = $user->username;
    } else {
        $userval = "";
    }	

    $form = "";
    
    $form .= "<p>" . __('logbrowser:user');
    $form .= view('input/text',array(
        'name' => 'search_username',
        'value' => $userval 
    )) . "</p>";

    $form .= "<p>" . __('logbrowser:starttime');
    $form .= view('input/text',array(
        'name' => 'timelower',
        'value' => $lowerval 
    )) . "</p>";

    $form .= "<p>" . __('logbrowser:endtime');
    
    $form .= view('input/text',array(
        'name' => 'timeupper',
        'value' => $upperval
    ))  . "</p>";
    
    $form .= view('input/submit',array(
        'value' => __('search')
    ));
                                                
    $wrappedform = view('input/form',array(
        'body' => $form,
        'method' => 'get',
        'action' => Config::get('url') . "admin/logbrowser"
    ));

    if ($upperval || $lowerval || $userval) {
        $hidden = "";
    } else {
        $hidden = "style=\"display:none\"";
    }
										
?>

		<div id="logbrowserSearchform" <?php echo $hidden; ?>><?php echo $wrappedform; ?></div>
		<p>
			<a href="javascript:void(0)" onclick="document.getElementById('logbrowserSearchform').style.display='block'"><?php echo __('logbrowser:search'); ?></a>
		</p>
	</div>
    
<?php
    echo view('navigation/pagination', $vars);
?>
<table class="log_entry">
<?php
    foreach ($vars['entries'] as $entry)
    {
        echo view("logbrowser/entry", array('entry' => $entry));
    }
?>
</table>