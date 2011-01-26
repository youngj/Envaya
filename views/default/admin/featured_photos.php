<?php
    $photos = $vars['photos'];
    
    foreach ($photos as $photo)
    {
        echo "<div style='float:right'>";
        
        echo ($photo->active) ? "(Active)" : "<div style='color:#999'>(Inactive)</div>";
        
        echo "<br />";
        
        echo "<a href='/admin/edit_featured_photo?guid={$photo->guid}'>Edit</a><br />";
        echo view('output/confirmlink', array(
            'text' => 'Delete',
            'is_action' => true,
            'href' => "admin/delete_featured_photo?guid={$photo->guid}"
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