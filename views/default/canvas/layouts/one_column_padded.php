<?php
    echo view("canvas/layouts/one_column", array(
        'area1' => $vars['area1'],
        'area2' => "<div class='section_content padded'>".$vars['area2']."</div>",
        'area3' => @$vars['area3']
    ));        
?>
