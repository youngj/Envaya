<?php
    echo view('page_elements/css', $vars, 'default');
    
    echo "<style type='text/css'>";
    echo escape(render_custom_view('css/custom', $vars));
    echo "</style>";
