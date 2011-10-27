<?php
    $org = $vars['org'];
    
    echo "<div class='search_listing'>";
    echo "<div class='search_listing_icon'>";
    echo view('org/icon', array('org' => $org));
    echo "</div>";
    echo "<div class='search_listing_info'>";
    echo "<div><b>" . escape($org->name) . "</b></div>";
    echo "<span style='font-size:10px'>".
            escape($org->get_location_text()).
            "<br />".
            "<span class='search_url'>".$org->get_url()."</span>".
            "</span>";
    echo "</div>";
    echo "</div>";