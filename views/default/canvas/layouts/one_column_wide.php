<div class="content_container">
    <div class="wide_column">  
        <?php 
            echo @$vars['area3']; 
            echo SessionMessages::view_all();
        ?>
        <div id='content'>
            <?php echo $vars['area2']; ?>
            <div style='clear:both'></div>        
        </div>
    </div>
</div>
<div class="footer_container">
    <?php echo get_submenu_group('footer', 'canvas_header/link_submenu', 'canvas_header/footer_submenu_group'); ?>
    <div class='language'>        
        <?php echo view('page_elements/language_links'); ?>    
    </div>
</div>
