<?php

    /**
     * Elgg pageshell
     * The standard HTML page shell that everything else fits into
     * 
     * @package Elgg
     * @subpackage Core
     * @author Curverider Ltd
     * @link http://elgg.org/
     * 
     * @uses $vars['config'] The site configuration settings, imported
     * @uses $vars['title'] The page title
     * @uses $vars['body'] The main content of the page
     * @uses $vars['messages'] A 2d array of various message registers, passed from system_messages()
     */

    // Set the content type
    header("Content-type: text/html; charset=UTF-8");
?>

<?php echo elgg_view('page_elements/header', $vars); ?>
<?php echo elgg_view('page_elements/topbar', $vars); ?>

<?php echo elgg_view('messages/list', array('object' => $vars['sysmessages'])); ?>

<?php echo $vars['body']; ?>

<?php echo elgg_view('page_elements/footer', $vars); ?>
