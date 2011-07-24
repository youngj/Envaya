<?php
    $url = url_with_param($vars['original_url'],'trans',$vars['mode']);
    echo "<a href='".escape($url)."'>".$vars['text']."</a>";
