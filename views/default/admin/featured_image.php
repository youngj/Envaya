<img id='image_preview' class='image_right' src='' />
<?php
    $org = $vars['org'];
    
    echo view('input/text',
        array(
            'internalname' => $vars['internalname'],
            'internalid' => 'image_url',
            'js' => 'onchange="updatePreview()" style="width:350px"',
            'value' => $vars['value'],
        )
    );          
    
    $files = ElggFile::query()->where('container_guid=?',$org->guid)->where('size=?','small')->limit(19)->filter();
    if ($files)
    {    
        echo "<div style='clear:both;padding-top:5px'>";    
                
        $urls = array($org->getIcon('medium'));
        foreach ($files as $file)
        {
            $urls[] = $file->getURL();
        }
        
        foreach ($urls as $url)
        {
            echo "<a href='javascript:void(0)'  onclick='setImageUrl(".json_encode($url).")'><img style='width:50px' src='".escape($url)."' /></a> ";
        }
        echo "</div>";
    }
?>
<script type='text/javascript'>
function setImageUrl(url)
{
    document.getElementById('image_url').value = url;    
    updatePreview();
}
function updatePreview()
{
    setTimeout(function() {
        document.getElementById('image_preview').src = document.getElementById('image_url').value;
    }, 1);
}
updatePreview();
</script>
