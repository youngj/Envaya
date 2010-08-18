<?php

		$entities = $vars['entities'];
		if (is_array($entities) && sizeof($entities) > 0) {
			
?>

		<table class="search_gallery">

<?php
			
			$col = 0;
			foreach($entities as $entity) {
				if ($col == 0) {
					
					echo "<tr>";
					
				}
				echo "<td class=\"search_gallery_item\">";
				echo view_entity($entity);
				echo "</td>";
				$col++;
				if ($col > 3) {
					echo "</tr>";
					$col = 0;
				}					
			}
			if ($col > 0) echo "</tr>";
			
?>

		</table>

<?php
			
		}
		
?>