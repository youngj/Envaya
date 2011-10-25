<div class='section_content padded'>

<?php

$user = $vars['user'];
$user_domain_names = UserDomainName::query()->where('guid = ?', $user->guid)->filter();

if ($user_domain_names)
{
    echo "<label>Domain Names</label><br />";
    echo "<ul>";
    foreach ($user_domain_names as $user_domain_name)
    {
        $domain_name = $user_domain_name->domain_name;
        echo "<li><a href='http://".escape($domain_name)."'>".escape($domain_name)."</a>";
        echo " <span class='admin_links'>";
        echo view('input/post_link', array(
            'text' => "(".__('delete').")",
            'confirm' => __('areyousure'),
            'href' => "{$user->get_url()}/delete_domain?id={$user_domain_name->id}",
        ));
        echo "</span></li>";
    }
    echo "</ul>";
}
?>

<form method='POST' action='<?php echo $user->get_url() ?>/add_domain'>
<?php echo view('input/securitytoken'); ?>
<div class='input'>
<label>Add Domain Name</label><br />
<?php echo view('input/text', array('name' => 'domain_name')); ?>
</div>
<?php echo view('input/submit', array('value' => __('save'))); ?>
</form>
</div>