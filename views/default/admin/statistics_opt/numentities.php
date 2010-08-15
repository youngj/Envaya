<?php
	/**
	 * Elgg statistics screen
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 */

	
	// Get entity statistics
	$entity_stats = Statistics::get_entity_stats();
	$even_odd = "";
?>
<div class="admin_statistics">
    <h3><?php echo __('admin:statistics:label:numentities'); ?></h3>
    <table>
        <?php
            foreach ($entity_stats as $k => $entry)
            {
            	arsort($entry);
                foreach ($entry as $a => $b)
                {
                    
                    //This function controls the alternating class
                	$even_odd = ( 'odd' != $even_odd ) ? 'odd' : 'even';

                    if ($a == "__base__") {
                    	$a = __("item:{$k}");
                    	if (empty($a)) 
                        	$a = $k;
                    }
                    else {
                    		if (empty($a))
                    			$a = __("item:{$k}");
                    		else
                				$a = __("item:{$k}:{$a}");
                    		if (empty($a)) {
								$a = "$k $a";
                    		}
                    	  }
                    echo <<< END
                        <tr class="{$even_odd}">
                            <td class="column_one">{$a}:</td>
                            <td>{$b}</td>
                        </tr>
END;
                }
            }
        ?>
    </table>
</div>