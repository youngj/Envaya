<div class='section_content padded'>
<?php
    $widget = $vars['widget'];
    
    $org = $widget->get_root_container_entity();
    
    $sectors = $org->get_sectors();

	sort($sectors);

	$sectorOptions = OrgSectors::get_options();
	$sectorNames = array();

	foreach ($sectors as $sector)
	{
		$sectorNames[] = "<a href='/org/browse?list=1&sector=$sector'>".escape($sectorOptions[$sector])."</a>";
	}

	echo implode(', ', $sectorNames);

	if (in_array(OrgSectors::Other, $sectors) && $org->sector_other)
	{
		echo " (".escape($org->sector_other).")";
	}       
?>
</div>