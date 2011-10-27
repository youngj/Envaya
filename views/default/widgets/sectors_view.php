<div class='section_content padded'>
<?php
    $widget = $vars['widget'];
    
    $org = $widget->get_container_user();
    
    $sectors = $org->get_sectors();

	sort($sectors);

	$sectorOptions = OrgSectors::get_options();
	$sectorNames = array();

	foreach ($sectors as $sector)
	{
		$sectorNames[] = "<a href='/pg/browse?list=1&sector=$sector'>".escape($sectorOptions[$sector])."</a>";
	}

	echo implode(', ', $sectorNames);

	if (in_array(OrgSectors::Other, $sectors) && $org->get_metadata('sector_other'))
	{
		echo " (".escape($org->get_metadata('sector_other')).")";
	}       
?>
</div>