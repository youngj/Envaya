<?php
    $org = $vars['org'];
    echo "<li><a href='{$org->get_url()}'><b>" . escape($org->name) . "</b></a></li>";
