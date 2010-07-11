<?php

    $entity = $vars['entity'];
    $url = $entity->getURL();
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
        echo $entity->renderContent();
        echo "<div class='blog_date'>{$entity->getDateText()}</div>";
    ?>
    <div style='clear:both'></div>
</div>

<?php

    if (!$full)
    {
        echo "</div>";
    }