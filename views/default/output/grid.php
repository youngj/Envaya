<?php
    $columns = $vars['columns'];
    $rows = json_decode($vars['value'], true);
    
    if (is_array($rows))
    {
?>
<table class='gridTable'>
<thead>
<tr>
    <?php 
        foreach ($columns as $column_id => $column)
        {
            echo "<th>".escape($column['label'])."</th>";
        }
    ?>
</tr>
</thead>
<tbody>
    <?php 
        foreach ($rows as $row)
        {
    ?>
    <tr>
        <?php 
            foreach ($columns as $column_id => $column)
            {                
                $res = view((@$column['multiline'] ? 'output/longtext' : 'output/text'), 
                    array('value' => @$row[$column_id]));
                echo "<td>$res</td>";
            }
        ?>    
    </tr>
    <?php
        }
    ?>    
</tbody>
</table>
<?php
    }
?>