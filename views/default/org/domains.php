<div class='section_content padded'>

<?php

$org = $vars['org'];
$org_domain_names = OrgDomainName::query()->where('guid = ?', $org->guid)->filter();

if ($org_domain_names)
{
    echo "<label>Domain Names</label><br />";
    echo "<ul>";
    foreach ($org_domain_names as $org_domain_name)
    {
        $domain_name = $org_domain_name->domain_name;
        echo "<li><a href='http://".escape($domain_name)."'>".escape($domain_name)."</a>";
        echo " <span class='admin_links'>";
        echo view('output/confirmlink', array(
            'text' => "(".__('delete').")",
            'href' => "{$org->get_url()}/delete_domain?id={$org_domain_name->id}",
        ));
        echo "</span></li>";
    }
    echo "</ul>";
}
?>

<form method='POST' action='<?php echo $org->get_url() ?>/add_domain'>
<?php echo view('input/securitytoken'); ?>
<div class='input'>
<label>Add Domain Name</label><br />
<?php echo view('input/text', array('name' => 'domain_name')); ?>
</div>
<?php echo view('input/submit', array('value' => __('save'))); ?>
</form>
</div>