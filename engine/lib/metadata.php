<?php

/**
 * Convert a database row to a new ElggMetadata
 *
 * @param stdClass $row
 * @return stdClass or ElggMetadata
 */
function row_to_elggmetadata($row)
{
    if (!($row instanceof stdClass))
        return $row;

    return new ElggMetadata($row);
}


/**
 * Get a specific item of metadata.
 *
 * @param $id int The item of metadata being retrieved.
 */
function get_metadata($id)
{
    return row_to_elggmetadata(
        get_data_row("SELECT * FROM metadata WHERE id = ?", array($id))
    );
}

function remove_metadata($entity_guid, $name)
{
    return delete_data("DELETE from metadata WHERE entity_guid = ? and name = ?",  array($entity_guid, $name));
}

function get_metadata_byname($entity_guid, $name)
{
    return row_to_elggmetadata(get_data_row(
        "SELECT * from metadata where entity_guid=? and name=? LIMIT 1", array($entity_guid, $name)
    ));
}

function get_metadata_for_entity($entity_guid)
{
    return array_map('row_to_elggmetadata', get_data(
        "SELECT * from metadata where entity_guid=?", array($entity_guid)
    ));
}


/**
 * Clear all the metadata for a given entity, assuming you have access to that metadata.
 *
 * @param int $guid
 */
function clear_metadata($entity_guid)
{
    return delete_data("DELETE from metadata where entity_guid=?", array($entity_guid));
}

function detect_value_type($value)
{
    if (is_array($value))
        return 'json';
    if (is_int($value))
        return 'integer';
    if (is_numeric($value))
        return 'text'; // todo?
    return 'text';
}