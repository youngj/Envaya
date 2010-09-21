<?php

    $entity = $vars['entity'];
    $url = $entity->get_url();
    $full = $vars['full'];

    $nextUrl = $url . "/next";
    $prevUrl = $url . "/prev";

    if (!$full)
    {
        echo "<div class='blog_post_wrapper padded'>";
    }
?>

<div class="blog_post">

    <?php
    
        echo $entity->render_content();

        echo "<div class='blog_date'>";
        if (!$full)
        {
            echo "<a href='{$entity->get_url()}'>{$entity->get_date_text()}</a>";
        }
        else
        {
            echo $entity->get_date_text();
        }
        echo "</div>";
    ?>
    <div style='clear:both'></div>
</div>

<?php

    if (!$full)
    {
        echo "</div>";
    }