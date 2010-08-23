<?php
    $feedItems = $vars['items'];

if (empty($feedItems))
{
    echo "<div class='padded'>".__("search:noresults")."</div>";
}

foreach ($feedItems as $feedItem)
{
    $org = $feedItem->get_user_entity();
    $subject = $feedItem->get_subject_entity();
    if ($org && $subject)
    {
    ?>

    <div class='blog_post_wrapper padded'>
    <div class="feed_post">
        <?php
            echo $feedItem->render_view($mode='self');
        ?>
        <div class='blog_date'><?php echo $feedItem->get_date_text() ?></div>
        <div style='clear:both'></div>
    </div>
    </div>
    <?php
    }
}
