<?php

    $size = strtolower(get_input('size'));
    if (!in_array($size,array('large','medium','small')))
        $size = "large";    

    output_image($widget->getImageFile($size));
?>