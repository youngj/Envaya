<?php
    $org = $vars['entity'];
?>

<script type='text/javascript'>

function selectFeaturedPhoto()
{
    function getURLForImg(img) {
    
        var src = img.src.replace("/small.jpg", "/large.jpg").replace("/medium.jpg","/large.jpg");
    
        return "/admin/add_featured_photo?image_url=" + encodeURIComponent(src) + 
            "&href=" + encodeURIComponent(window.location.href) + 
            "&user_guid=<?php echo $org->guid; ?>";
    }

    function createChooserImg(img)
    {
        var chooserImg = createElem('img');
        chooserImg.style.cursor = 'pointer';
        chooserImg.style.textAlign = 'center';
        
        chooserImg.src = img.src;
        addEvent(chooserImg,'click', function() { window.location.href = getURLForImg(chooserImg); });
        return chooserImg;
    }
    
    var imgs = document.getElementsByTagName('img');
    
    var validImgs = [];
    
    for (var i = 0; i < imgs.length; i++)
    {
        var img = imgs[i];
        if (img.width > 100 && img.height > 100)
        {
            validImgs.push(img);
        }
    }
    
    if (validImgs.length < 1)
    {
        alert("No photos found on this page");
    }
    else if (validImgs.length == 1)
    {
        window.location.href = getURLForImg(validImgs[0]);
    }
    else
    {
        var chooser = createElem('div');
        
        chooser.appendChild(document.createTextNode('Choose the photo you want to feature:'));        
        
        for (var i = 0; i < validImgs.length; i++)
        {
            chooser.appendChild(createElem('br'));
            chooser.appendChild(createChooserImg(validImgs[i]));
        }
        removeChildren(document.body);
        document.body.appendChild(chooser);
    }
    
}

</script>

<div class='adminBox'>
<?php

if ($org->approval == 0)
{
    echo view('output/confirmlink', array(
        'text' => __('approval:approve'),
        'is_action' => true,
        'href' => "admin/approve?org_guid={$org->guid}&approval=2"
    ));
    echo " ";
    echo view('output/confirmlink', array(
        'text' => __('approval:reject'),
        'is_action' => true,
        'href' => "admin/approve?org_guid={$org->guid}&approval=-1"
    ));
    echo " ";
}
else
{
    echo view('output/confirmlink', array(
        'text' => __($org->approval > 0 ? 'approval:unapprove' : 'approval:unreject'),
        'is_action' => true,
        'href' => "admin/approve?org_guid={$org->guid}&approval=0"
    ));
    echo " ";
}

if ($org->approval < 0)
{
    echo view('output/confirmlink', array(
        'text' => __('approval:delete'),
        'is_action' => true,
        'href' => "admin/delete_entity?guid={$org->guid}&next=/admin/user"
    ));
    echo " ";
}

echo "<a href='/admin/add_featured?username={$org->username}'>".__('featured:add')."</a>";        
echo " ";
echo "<a href='javascript:void(0)' onclick='javascript:selectFeaturedPhoto()'>".__('featured_photo:add')."</a>";        
echo " ";
echo "<a href='/{$org->username}/dashboard'>".__('dashboard:title')."</a>";
echo " ";
echo "<a href='/{$org->username}/settings'>".__('help:settings')."</a>";
echo " ";
echo "<a href='/{$org->username}/username'>".__('username:title')."</a>";
echo " ";
echo "<a href='/{$org->username}/domains'>".__('domains:edit')."</a>";
echo " ";

echo get_submenu_group('org_actions', 'canvas_header/link_submenu', 'canvas_header/basic_submenu_group'); 

?>
</div>