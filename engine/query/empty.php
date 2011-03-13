<?php

class Query_Empty extends Query_Select
{
    function count() { return 0; }
    function filter() { return array(); }
}