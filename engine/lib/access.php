<?php

    /**
     * Override the default behaviour and allow results to show hidden entities as well.
     * THIS IS A HACK.
     *
     * TODO: Replace this with query object!
     */
    $ENTITY_SHOW_HIDDEN_OVERRIDE = false;

    /**
     * This will be replaced. Do not use in plugins!
     *
     * @param bool $show
     */
    function access_show_hidden_entities($show_hidden)
    {
        global $ENTITY_SHOW_HIDDEN_OVERRIDE;
        $ENTITY_SHOW_HIDDEN_OVERRIDE = $show_hidden;
    }

    /**
     * This will be replaced. Do not use in plugins!
     */
    function access_get_show_hidden_status()
    {
        global $ENTITY_SHOW_HIDDEN_OVERRIDE;
        return $ENTITY_SHOW_HIDDEN_OVERRIDE;
    }

    function get_access_sql_suffix($table_prefix = "",$owner=null)
    {
        global $ENTITY_SHOW_HIDDEN_OVERRIDE;

        if ($table_prefix)
        {
            $table_prefix = $table_prefix . ".";
        }

        $sql = " (1 = 1) ";

        if (!$ENTITY_SHOW_HIDDEN_OVERRIDE)
            $sql .= " and {$table_prefix}enabled='yes'";
        return '('.$sql.')';
    }

    function has_access_to_entity($entity, $user = null) {

        global $ENTITY_SHOW_HIDDEN_OVERRIDE;

        if ($ENTITY_SHOW_HIDDEN_OVERRIDE)
        {
            return true;
        }
        else
        {
            return $entity->enabled == 'yes';
        }
    }