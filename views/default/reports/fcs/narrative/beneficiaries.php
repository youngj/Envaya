<?php

/*


<div class='input'>
<label>C. Who is benefitting from your project?</label><br />
<?php
if ($edit) {
    echo "<div class='help'>Indicate the number of people in each category your project is reaching. Do not count one person more than one time.</div>";
}

$constituency_fields = function($name) use ($input, $report)
{    
    $female = $report->get_field($name."_female")->$input();
    $male = $report->get_field($name.'_male')->$input();

    return "<table style='margin:10px 15px'><tr><th colspan='2'>".__("fcs:$name")."</th></tr>".
        "<tr><td>Female</td><td style='padding-left:10px'>$female</td></tr>".
        "<tr><td>Male</td><td style='padding-left:10px'>$male</td></tr></table>";
}

?>

<table>
<tr>
<td><?php echo $constituency_fields('widows'); ?></td>
<td><?php echo $constituency_fields('elderly'); ?></td>
<td><?php echo $constituency_fields('refugees'); ?></td>
</tr>
<tr>
<td><?php echo $constituency_fields('poor'); ?></td>
<td><?php echo $constituency_fields('orphans'); ?></td>
<td><?php echo $constituency_fields('unemployed'); ?></td>
</tr>
<tr>
<td><?php echo $constituency_fields('hiv_aids'); ?></td>
<td><?php echo $constituency_fields('children'); ?></td>
<td><?php echo $constituency_fields('youth'); ?></td>
</tr>
<tr>
<td><?php echo $constituency_fields('homeless'); ?></td>
<td><?php echo $constituency_fields('disabled'); ?></td>
<td><?php echo $constituency_fields('other'); ?></td>
</tr>
</table>
</div>

<?php
echo $report->get_field('other_details')->$html();
?>

*/

?>