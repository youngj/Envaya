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
                $args = @$column['args'] ?: array();
                $output_args = @$column['output_args'];
                if ($output_args)
                {
                    foreach ($output_args as $k => $v)
                    {
                        $args[$k] = $v;
                    }   
                }
                $args['value'] = @$row[$column_id];
            
                $res = view((@$column['output_type'] ?: 'output/text'), $args);                    
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