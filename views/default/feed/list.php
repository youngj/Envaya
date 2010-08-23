<?php
    $feedItems = $vars['items'];

if (empty($feedItems))
{
    echo "<div class='padded'>".__("search:noresults")."</div>";
}

$isAdmin = Session::isadminloggedin();

foreach ($feedItems as $feedItem)
{
    $org = $feedItem->get_user_entity();
    $subject = $feedItem->get_subject_entity();
    if ($org && $subject)
    {
        $orgIcon = $org->get_icon('small');
        $orgUrl = $org->get_url();

    ?>

    <div class='blog_post_wrapper padded'>
    <div class="feed_post">
        <a class='feed_org_icon' href='<?php echo $orgUrl ?>'><img src='<?php echo $orgIcon ?>' /></a>
        <div class='feed_content'>
        <?php
            echo $feedItem->render_view();
        ?>
        <div class='blog_date'><?php echo $feedItem->get_date_text() ?></div>
        </div>
        <div style='clear:both'></div>
    </div>
    </div>
    <?php
    }
}
