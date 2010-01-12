<?php

	/**
	 * Elgg header contents
	 * This file holds the header output that a user will see
	 *
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 **/

?>

<div id="page_container">
<div id="page_wrapper">

<div id="layout_header">
<div id="wrapper_header">
	<!-- display the page title -->

    <div style='float:right'>
    
    <?php    
        echo elgg_view("page_elements/select_language"); 
    ?>
    </div>

    <div style='float:right;padding-right:30px;'>
    <?php    
        if (isloggedin())
        {
            echo "<a href='".$CONFIG->wwwroot."action/logout'>".elgg_echo("logout")."</a>";
        }
        else
        {
            echo "<a href='".$CONFIG->wwwroot."pg/login'>".elgg_echo("login")."</a>";
        }        
    ?>
    </div>

	<h1><a href="<?php echo $vars['url']; ?>">
		<img src='<?php echo $vars['url']; ?>/mod/envaya/graphics/logo.gif' width='182' height='34' alt='Envaya' />
	</a></h1>
    
    
</div><!-- /#wrapper_header -->
</div><!-- /#layout_header -->
