<div class='section_content padded'>

<form method='POST' action='<?php echo escape($_SERVER['REQUEST_URI']); ?>'>

<div class='instructions'>
<?php echo __('register:duplicate_instructions'); ?>
</div>

<?php

foreach ($vars['duplicates'] as $dup)
{
    $url = url_with_param($vars['login_url'],'username',$dup->username);
    
    echo "<div class='search_listing'>";
    echo "<div class='search_listing_icon' style='padding-top:4px'>";    
    echo "<a href='{$url}'>".view('account/icon', array('user' => $dup))."</a>";
    echo "</div>";
    echo "<div class='search_listing_info'>";
    echo "<div><b><a href='{$url}'>" . escape($dup->name) . "</a></b></div>";
    echo "<span style='font-size:10px'>".escape($dup->get_location_text());
    echo "<br />Username: {$dup->username}</span>";
    echo "</div>";
    echo "</div>";
}

?>

<div class='instructions'>
<?php echo __('register:not_duplicate_instructions'); ?>
</div>

<?php

echo view('input/hidden', array('name' => 'ignore_possible_duplicates', 'value' => '1'));

echo view('input/hidden_multi', array('fields' => $_POST));

echo view('input/submit', array('value' => __('register:not_duplicate')));

?>
</form>
</div>	