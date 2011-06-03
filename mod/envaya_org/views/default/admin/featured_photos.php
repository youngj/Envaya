<div class='section_content padded'>
<?php
    $photos = $vars['photos'];
    
    foreach ($photos as $photo)
    {
        echo "<div style='float:right'>";
        
        echo ($photo->active) ? "(Active)" : "<div style='color:#999'>(Inactive)</div>";
        
        echo "<br />";
        
        echo "<a href='/admin/envaya/edit_featured_photo?guid={$photo->guid}'>Edit</a><br />";
        echo view('input/post_link', array(
            'text' => 'Delete',
            'confirm' => __('areyousure'),            
            'href' => "admin/envaya/edit_featured_photo?guid={$photo->guid}&delete=1"
        ));
        echo "</div>";
        echo view('admin/preview_featured_photo', array(
            'image_url' => $photo->image_url, 
            'x_offset' => $photo->x_offset,  
            'y_offset' => $photo->y_offset,
            'org_name' => $photo->org_name,
            'caption' => $photo->caption,
            'href' => $photo->href,
        ));        
    }
?>

<a href='/admin/envaya/add_featured_photo'>Add Featured Photo</a>
</div>