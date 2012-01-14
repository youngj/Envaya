<div class='section_content padded'>
<ul>
<li>
    <a href='/org/featured'>Featured Organizations</a>
</li>
<li>
    <a href='/admin/envaya/featured_photos'>Featured Photos</a>
</li>
</ul>

<form method='POST' action='/admin/envaya/home_page'>
<?php 
    echo view('input/securitytoken');
    
    $guid = State::get('home_bottom_left_guid');

    echo "<div class='input'>";
    echo "<label>ID of widget in bottom-left corner</label><br />";
    echo view('input/text', array(
        'name' => 'home_bottom_left_guid',
        'value' => $guid,
        'style' => 'width:80px',
    ));
    
    if ($guid)
    {
        echo " <a href='/$guid/edit'>Edit Content</a>";
    }
    
    echo "</div>";
    
    
    
    echo view('input/submit', array('value' => __('savechanges')));
?>
</form>
</div>