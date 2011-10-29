<?php
    $org = $vars['org'];    
?>
<div class="search_listing">
<div class="search_listing_icon"><?php 
    echo "<a href='{$org->get_url()}'>".view('account/icon', array('user' => $org))."</a>";
?></div>
<div class="search_listing_info"><div><b><?php 
    echo "<b><a href='{$org->get_url()}'>" . escape($org->name) . "</a>";
?></b></div></div>		
</div>
