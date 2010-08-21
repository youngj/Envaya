<?php

class ReportDefinition extends Entity
{
    static $subtype_id = T_report_definition;
    static $table_name = 'report_definitions';
    static $table_attributes = array(
        'handler_class' => '',
        'name' => '',
    );
}