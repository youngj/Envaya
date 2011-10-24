<div class='instructions'>
<?php echo __('register:user:instructions'). " ";
echo sprintf(__('register:if_org'), "<a href='/org/new?next=".urlencode($vars['next'])."'>".__('clickhere')."</a>"); 
?>
</div>
