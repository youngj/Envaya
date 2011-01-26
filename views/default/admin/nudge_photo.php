<?php
    $photo_id = $vars['photo_id'];
    $x_offset_id = $vars['x_offset_id'];
    $y_offset_id = $vars['y_offset_id'];
?>

<script type='text/javascript'>

function nudgePhoto(dx, dy)
{
    var photo = document.getElementById(<?php echo json_encode($photo_id); ?>);
    
    var xOffsetField = document.getElementById(<?php echo json_encode($x_offset_id); ?>);
    var yOffsetField = document.getElementById(<?php echo json_encode($y_offset_id); ?>);
    
    var xOffset = parseInt(xOffsetField.value,10);
    var yOffset = parseInt(yOffsetField.value,10);
    
    xOffset += dx;
    yOffset += dy;
    photo.style.left = (-xOffset) + "px";
    photo.style.top = (-yOffset) + "px";
    
    xOffsetField.value = xOffset;
    yOffsetField.value = yOffset;    
}

</script>

<div style='text-align:center;font-weight:bold'>
    <a href='javascript:void(0)' onclick='javascript:nudgePhoto(0,5)'>^^</a>    
    <br />    
    <a href='javascript:void(0)' onclick='javascript:nudgePhoto(0,1)'>^</a>    
    <br />    
    <a href='javascript:void(0)' onclick='javascript:nudgePhoto(5,0)'>&lt;&lt;</a>    
    <a href='javascript:void(0)' onclick='javascript:nudgePhoto(1,0)'>&lt;</a>
    Nudge
    <a href='javascript:void(0)' onclick='javascript:nudgePhoto(-1,0)'>&gt;</a>
    <a href='javascript:void(0)' onclick='javascript:nudgePhoto(-5,0)'>&gt;&gt;</a>    
    <br />
    <a href='javascript:void(0)' onclick='javascript:nudgePhoto(0,-1)'>v</a>
    <br />
    <a href='javascript:void(0)' onclick='javascript:nudgePhoto(0,-5)'>vv</a>    
</div>