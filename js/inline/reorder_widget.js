function asyncReorderWidget($url, $table_id, $link_id, ts, token)
{
    var xhr = jsonXHR(function(res) {             
        var guids = res.guids;        
        var rows = {};        
        var tbody = $($table_id);

        for (var i = 0; i < guids.length; i++)
        {
            var guid = guids[i];
            var row = $($table_id + '_' + guid);
            if (row)
            {
                removeElem(row);
                rows[guid] = row;
            }
        }
        
        for (var i = 0; i < guids.length; i++)
        {
            var guid = guids[i];
            var row = rows[guid];
            if (row)
            {
                tbody.appendChild(row);                
                var up = $($table_id + '_' + guid + '_up');
                if (up) 
                {
                    up.style.display = (i == 0) ? 'none' : 'inline';
                }
            }
        }
    });
        
    var link = $($link_id);        
    link.style.display = 'none';
    
    asyncPost(xhr, $url, {__ts:ts, __token:token});
}

function highlightRow($row_id, $active)
{
    var row = $($row_id);
    if (row)
    {
        row.style.backgroundColor = $active ? '#f0f0f0' : '';
    }
}