<?php
    if (!@$vars['no_top_bar']) 
    {
        $user = Session::get_logged_in_user();
?>
<div id="topbar">
<div id='top_links'>
    <a id='logoContainer' href="/home">
        <img src="/_media/images/logo3.gif" alt="Envaya" width="28" height="25">
    </a>      
    <a href='/envaya'><?php echo __('about') ?></a>
    <a href='/pg/browse'><?php echo __('browse') ?></a>
    <a href='/pg/search'><?php echo __('search') ?></a>   
    <a href='/pg/volunteer'><?php echo __('volunteer') ?></a>    
    <a href='/pg/feed'><?php echo __('feed') ?></a>        
</div>
<?php
    echo "<div class='top_language'>";

    echo view('js/language');
    echo view('input/pulldown', array(
        'name' => 'top_language',
        'id' => 'top_language',
        'options' => Language::get_options(),
        'value' => Language::get_current_code(),
        'attrs' => array(
            'onchange' => 'languageChanged()',
            'onkeypress' => 'languageChanged()',
        )
    ));        
    echo "</div>";

    echo view('page_elements/login_area', $vars);    

?>    
<div style="clear:both"></div>
</div>
<?php
}