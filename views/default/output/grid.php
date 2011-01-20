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
        $row_num = 1;
    
        foreach ($rows as $row)
        {
    ?>
    <tr>
        <?php                
            foreach ($columns as $column_id => $column)
            {              
                $res = ReportFieldDefinition_Grid::render_cell_value(@$row[$column_id], $column);            
                echo "<td>$res</td>";
            }
        ?>    
    </tr>
    <?php
            $row_num++;
        }
    ?>    
</tbody>
</table>
<?php
    }
?>