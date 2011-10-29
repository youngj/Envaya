<?php
    $org = $vars['org'];
    
    if (Permission_EditMainSite::has_for_root())
    {
?>
<?php echo view('js/dom'); ?>
<script type='text/javascript'>
function selectFeaturedPhoto()
{
    function getURLForImg(img) {
    
        var src = img.src.replace("/small.jpg", "/large.jpg").replace("/medium.jpg","/large.jpg");
    
        return "/admin/envaya/add_featured_photo?image_url=" + encodeURIComponent(src) + 
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
<?php
        echo "<a href='/admin/envaya/add_featured?username={$org->username}'>".__('featured:add')."</a>";        
        echo " ";
        echo "<a href='javascript:void(0)' onclick='javascript:selectFeaturedPhoto()'>".__('featured:photo:add')."</a>";
    }