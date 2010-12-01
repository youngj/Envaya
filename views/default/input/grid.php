

<?php
    global $GRID_INCLUDE_COUNT;
    
    if (!isset($GRID_INCLUDE_COUNT))
    {
        $GRID_INCLUDE_COUNT = 0;
    }
    else
    {
        $GRID_INCLUDE_COUNT++;
    }
    
    echo "<div id='grid_container{$GRID_INCLUDE_COUNT}' class='grid-container'></div>";
    echo "<input type='hidden' name='{$vars['internalname']}' id='grid_value{$GRID_INCLUDE_COUNT}' value='".escape(@$vars['value'])."' />";    

    ob_start();
?>
<link rel="stylesheet" href="/_media/slickgrid/slick.grid.merged.css" type="text/css" media="screen" charset="utf-8" />        
<script type='text/javascript' src="/_media/slickgrid/jquery-1.4.3.min.js"></script>
<script type='text/javascript' src="/_media/slickgrid/jquery-ui-1.8.5.custom.min.js"></script>
<script type='text/javascript' src="/_media/slickgrid/slick.editors.js?v3"></script>
<script type='text/javascript' src="/_media/slickgrid/slick.grid-1.4.3.merged.min.js"></script>
<script type='text/javascript' src="/_media/slickgrid/json.js"></script>
<script type='text/javascript'>
var all_slickgrids = {};

function setDirty(){}

function isEmptyGridItem(grid, item)
{   
    var columns = grid.getColumns();
    for (var i = 0; i < columns.length; i++)
    {
        if (item[columns[i].field])
            return false;
    }   
    return true;
}

function removeGridRow(gridNum, gridRow) 
{
    var grid = all_slickgrids[gridNum];
    var data = grid.getData();
    var item = data[gridRow];

    if (isEmptyGridItem(grid, item) || confirm("Are you sure you want to delete this row?"))
    {
        data.splice(gridRow,1);           
        grid.removeRow(gridRow);
        grid.updateRowCount();
        grid.onCellChange();
        grid.render();
    }
}

</script>
<?php    
    $slickgrid_header = ob_get_clean();
    PageContext::add_header_html('slickgrid', $slickgrid_header);
?>

<script>

$(function()
{
    var data = <?php         
        echo json_encode(json_decode($vars['value'], true) ?: array());
    ?>;
    
    for (var i = data.length; i < <?php echo @$vars['initial_rows'] ?: 3; ?>; i++)
    {
        data.push({});
    }

    var options = {
        editable: true,
        enableAddRow: <?php echo json_encode(isset($vars['enable_add_row']) ? $vars['enable_add_row'] : true); ?>,
        enableCellNavigation: true,
        asyncEditorLoading: false,
        autoHeight: true,
        forceFitColumns : true
    };
           
    var columns = [];
    
    <?php if (@$vars['show_row_num']) { ?>
        columns.push({ 
         id: '_row_num',
         name: '&nbsp;', 
         field: '_row_num', 
         width:30,
         unselectable: true,
         formatter: RowNumCellFormatter
        });
    <?php } ?>
    

    <?php 
        foreach ($vars['columns'] as $column_id => $column)
        {
            $jsonArgs  = array(
                'id' => $column_id,
                'field' => $column_id,
                'name' => @$column['label'] ?: '&nbsp;',
            );
            if (@$column['width'])
            {
                $jsonArgs['width'] = $column['width'];
            }
        
            echo "var col = " . json_encode($jsonArgs) . ";";
            
            if (!@$column['readonly'])
            {
                $editor = @$column['editor'] ?: (@$column['multiline'] ? 'TextareaCellEditor' : 'TextCellEditor');
                echo "col.editor = window[".json_encode($editor)."];";
                
                if (@$column['args'])
                {
                    echo "col.args = ".json_encode($column['args']).";";
                }
            }                       
            
            $formatter = @$column['formatter'] ?: (@$column['multiline'] ? 'TextareaCellFormatter' : 'TextCellFormatter');

            echo "col.formatter = window[".json_encode($formatter)."];";
            
            echo "columns.push(col);";
        }
    ?>

    if (options.enableAddRow)
    {
        columns.push({ 
                 id: '_delete',
                 name: '', 
                 field: '_delete', 
                 width:30,
                 unselectable: true,
                 formatter: function (r, c, id, def, datactx) { 
                    return '<a href="javascript:void(0)" class="gridDelete" onclick="removeGridRow(<?php echo $GRID_INCLUDE_COUNT; ?>,' + r + ')"></a>'; 
                }
        });
    }
        
    <?php 
        if (@$vars['row_height'])
        {
            echo "options.rowHeight = {$vars['row_height']};\n";
        }
    ?>

    var grid = new Slick.Grid($("#grid_container<?php echo $GRID_INCLUDE_COUNT; ?>"), data, columns, options);
          
    grid.onAddNewRow = function(item, columnDef) {
        data.push(item);        
        grid.removeRows([data.length-1]);
        grid.onCellChange();
        grid.updateRowCount();
        grid.render();
    };    
    
    grid.onCellChange = function()
    {
        var nonEmptyData = [];
        for (var i = 0; i < data.length; i++)
        {
            if (!isEmptyGridItem(grid, data[i]))
            {
                nonEmptyData.push(data[i]);
            }
        }    
        if (options.enableAddRow && nonEmptyData.length > data.length - 1)
        {
            data.push({});    
            grid.updateRowCount();
            grid.render();            
        }
        document.getElementById("grid_value<?php echo $GRID_INCLUDE_COUNT; ?>").value = JSON.serialize(nonEmptyData);
    };
    
    all_slickgrids[<?php echo $GRID_INCLUDE_COUNT; ?>] = grid;
    
    addSubmitFn(function()
    {
        Slick.GlobalEditorLock.commitCurrentEdit();
    });
});

</script>
