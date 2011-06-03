<div class='slideshow_container' style='position:relative;border:1px solid black;margin:4px auto;'>
<div class='slideshow_photo'>
    <img <?php 
            if (isset($vars['id'])) 
            { 
                echo "id='{$vars['id']}'"; 
            } 
    ?> style='left:<?php echo -1 * (int)$vars['x_offset']; ?>px;top:<?php echo -1 * (int)$vars['y_offset']; ?>px' 
    src='<?php echo escape($vars['image_url']); ?>' />
</div>
<div class='slideshow_shadow'></div>
<div class='slideshow_controls'>
<div class='slideshow_caption'>
<a href='<?php echo escape(@$vars['href']); ?>'><?php echo escape(@$vars['caption']); ?></a>
<a href='<?php echo escape(@$vars['href']); ?>'><?php echo escape(@$vars['org_name']); ?></a>
</div>
</div>
</div>