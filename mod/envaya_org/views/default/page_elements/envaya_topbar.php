<?php
    if (!@$vars['no_top_bar']) 
    {
?>
<div id="topbar">
<table class='topbarTable'>
<tr>
<td class='topbarLinks'>
    <a id='logoContainer' href="/home">
        <img src="/_media/images/logo2.gif" alt="Envaya" width="120" height="25">
    </a>
    <a href='/envaya'><?php echo __('about') ?></a>
    <a href='/pg/browse'><?php echo __('browse') ?></a>
    <a href='/pg/search'><?php echo __('search') ?></a>
    <a href='/pg/discussions'><?php echo __('discussions:title') ?></a>    
    <a href='/pg/feed'><?php echo __('feed') ?></a>    
    <div class='top_language'>
    <?php
        echo __('language');
        echo '&nbsp;';
        
        echo "<script type='text/javascript'>".view('js/language')."</script>";
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
    ?>
    </div>
</td>
<td width='159'>&nbsp;</td>
</tr>
</table>

<?php if (!@$vars['hide_login']) { ?>
<div id='topRight'><?php echo view('page_elements/login_area', $vars); ?>
</div>
<?php } ?>

</div>
<div style="clear:both"></div>
<?php
    }
?>