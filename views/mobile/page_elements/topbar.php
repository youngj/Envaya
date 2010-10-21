<div id="topbar">
    <a href="/home">
        <img src="/_graphics/logo.gif?v5" alt="Envaya" width="145" height="30">
    </a>
<div style='clear:both'></div>    
</div>
<div id='topbar2'>
<a href='/envaya'><?php echo __('about') ?></a>
<a href='/org/browse'><?php echo __('browse') ?></a>
<a href='/org/search'><?php echo __('search') ?></a> 
<a href='/org/feed'><?php echo __('feed') ?></a>    
<?php 
if (!@$vars['hideLogin']) 
{ 
    echo " ".view('page_elements/login_area', $vars); 
} 
?>
</div>