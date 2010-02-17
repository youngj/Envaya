
<?php

    /**
     * Elgg blog individual post view
     * 
     * @package ElggBlog
     * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
     * @author Ben Werdmuller <ben@curverider.co.uk>
     * @copyright Curverider Ltd 2008-2009
     * @link http://elgg.com/
     * 
     * @uses $vars['entity'] Optionally, the blog post to view
     */
                                    
    $url = $vars['entity']->getURL();
    $owner = $vars['entity']->getOwnerEntity();                    
    $canedit = $vars['entity']->canEdit();
?>

    
<div class="blog_post">    
    <?php echo escape($vars['entity']->title); ?>
        
    <p class="strapline">
        <?php

            echo sprintf(elgg_echo("blog:strapline"),
                            date("M j, Y",$vars['entity']->time_created)
            );

        ?>
               
    </p>
</div>
