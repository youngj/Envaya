<?php
	$sectors = $vars['sectors'];
	$other = $vars['sector_other'];

	sort($sectors);

	$sectorOptions = Organization::get_sector_options();
	$sectorNames = array();

	foreach ($sectors as $sector)
	{
		$sectorNames[] = "<a href='org/browse?list=1&sector=$sector'>".escape($sectorOptions[$sector])."</a>";
	}

	echo implode(', ', $sectorNames);

	if (in_array(SECTOR_OTHER, $sectors) && $other)
	{
		echo " (".escape($other).")";
	}