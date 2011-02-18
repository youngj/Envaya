<?php    
    $GRID_INCLUDE_COUNT = $vars['include_count'];
    
    $tableId = "inputGrid$GRID_INCLUDE_COUNT";
    $resultId = "grid_value{$GRID_INCLUDE_COUNT}";
    $columns = $vars['columns'];
    $rows = json_decode($vars['value'], true) ?: array();        
    
    $initial_rows = @$vars['initial_rows'] ?: 3;
    $show_row_num = $vars['show_row_num'];
    
    $enable_add_row = @$vars['enable_add_row'] || !isset($vars['enable_add_row']);
    
    if ($enable_add_row)
    {
        $rows[] = array();
    }    
    
    while (sizeof($rows) < $initial_rows)
    {
        $rows[] = array();
    }
       
    echo "<input type='hidden' name='{$vars['name']}' id='$resultId' value='".escape(@$vars['value'])."' />";     
?>

<table class='gridTable inputGrid' id='<?php echo $tableId; ?>'>
<thead>
<tr>
    <?php 
        if ($show_row_num)
        {
            echo "<th>&nbsp;</th>";
        }      
    
        foreach ($columns as $column_id => $column)
        {
            echo "<th class='column_$column_id'>".escape($column['label'])."</th>";
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
    <tr rownum='<?php echo $row_num; ?>'>
        <?php 
            if ($show_row_num)
            {
                echo "<th>{$row_num}</th>";
            }        
        
            foreach ($columns as $column_id => $column)
            {       
                $args = @$column['args'] ?: array();
                $input_args = @$column['input_args'];
                if ($input_args)
                {
                    foreach ($input_args as $k => $v)
                    {
                        $args[$k] = $v;
                    }   
                }
            
                $args['id'] = "{$tableId}_{$column_id}_{$row_num}";
                $args['js'] = (@$args['js'] ?: '') . " onchange='serializeGrid$GRID_INCLUDE_COUNT()'";
                $args['value'] = @$row[$column_id];
            
                $res = view(@$column['input_type'] ?: 'input/text', $args);
                echo "<td class='column_{$column_id}'>$res</td>";
            }            
        ?>    
    </tr>
    <?php
            $row_num++;
        }
    ?>    
</tbody>
</table>

<?php if ($enable_add_row) { ?> 
<a href='javascript:void(0)' id='grid_add_row<?php echo $GRID_INCLUDE_COUNT; ?>' style='display:none' onclick='saveChanges()'><?php echo __('grid:add_row'); ?></a>
<?php } ?>

<?php    
   
    ob_start();
?>

<style type='text/css'>
<?php
$row_height = @$vars['row_height'];
if ($row_height)
{
    echo "#{$tableId} .input-textarea, ";
    echo "#{$tableId} .input-text { height: {$row_height}px; } ";
}

foreach ($columns as $column_id => $column)
{
    $column_class = "column_{$column_id}";
    $width = @$column['width'];
    if ($width)
    {
        echo "#{$tableId} .{$column_class}, ";
        echo "#{$tableId} .{$column_class} .input-textarea, ";
        echo "#{$tableId} .{$column_class} .input-text { width: {$width}px; } ";
    }
}

?>
</style>
<?php    
    $this_grid_header = ob_get_clean();
    PageContext::add_header_html("grid_header_{$GRID_INCLUDE_COUNT}", $this_grid_header);
    
    ob_start();
?>

<script type='text/javascript' src='/_media/json.js'></script>
<script type='text/javascript'>

function getInputValue(input)
{
    return input.value;
}

</script>

<?php
    $grid_header = ob_get_clean();
    PageContext::add_header_html("grid_header_common", $grid_header);       
?>

<script type='text/javascript'>

function serializeGrid<?php echo $GRID_INCLUDE_COUNT; ?>()
{
    var tableValues = [];

    var table = document.getElementById('<?php echo $tableId; ?>');
    var tbody = table.getElementsByTagName('tbody')[0];
    var rows = tbody.getElementsByTagName('tr');
    
    var numRows = rows.length;
    for (var i = 0; i < numRows; i++)
    {
        var row = rows[i];
        var rowNum = parseInt(row.getAttribute('rownum'), 10);
        var rowValues = {};
        var blankRow = true;
    <?php 
        foreach ($columns as $column_id => $column)
        {                
    ?>
            var input = document.getElementById(<?php echo json_encode("{$tableId}_{$column_id}_"); ?> + rowNum);
            var val = getInputValue(input);
            rowValues[<?php echo json_encode($column_id); ?>] = val;
            if (val)
            {
                blankRow = false;
            }
    <?php
        }
    ?>
        if (!blankRow)
        {
            tableValues.push(rowValues);
        }
    }
    
    var resultInput = document.getElementById('<?php echo $resultId; ?>');
    resultInput.value = JSON.serialize(tableValues);
    
    <?php if ($enable_add_row) { ?> 
    if (tableValues.length == rows.length)
    {
        document.getElementById('grid_add_row<?php echo $GRID_INCLUDE_COUNT; ?>').style.display = 'block';
    }
    <?php } ?>
}
</script>