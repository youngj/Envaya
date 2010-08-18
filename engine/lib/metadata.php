<?php

/**
 * Convert a database row to a new EntityMetadata
 *
 * @param stdClass $row
 * @return EntityMetadata or null
 */
function row_to_metadata($row)
{
    if (!$row)
        return null;

    return new EntityMetadata($row);
}

function get_metadata_byname($entity_guid, $name)
{
    return row_to_metadata(get_data_row(
        "SELECT * from metadata where entity_guid=? and name=? LIMIT 1", array($entity_guid, $name)
    ));
}
