<?php

/*
 * Stub mixin for Entity classes used when the translate module is disabled.
 * When the translate module is enabled, it is replaced by the implementation defined there.
 */
class Mixin_Translatable extends Mixin
{
    public function translate_field($field, $lang = null)
    {
        return $this->$field;
    }
}