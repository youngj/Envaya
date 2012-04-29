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
    
    $guid = get_input('bottom_left_guid') ?: State::get('home_bottom_left_guid');
    $widget = Widget::get_by_guid($guid);

    echo "<div class='input'>";
    echo "<label>ID of widget in bottom-left corner</label><br />";
    echo view('input/text', array(
        'name' => 'home_bottom_left_guid',
        'value' => $widget ? $widget->guid : '',
        'style' => 'width:300px',
    ));
        
    if ($widget)
    {
        echo "<br />Preview:";
        echo "<div style='border:1px solid #ccc;width:420px;padding:5px'>";
        echo "<h4 class='home_featured_heading'>".escape($widget->get_title())."</h4>";
        echo "<div class='home_featured_content'>";
        echo $widget->render_content();
        echo "</div>";
        echo "</div>"; 
    
        echo " <a href='{$widget->get_edit_url()}'>Edit Content</a>";
    }
    
    echo "</div>";
    
    
    
    echo view('input/submit', array('value' => __('savechanges')));
?>
</form>
</div>