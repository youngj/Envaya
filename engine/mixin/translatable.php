<?php

class Mixin_Translatable extends Mixin
{
    public function translate_field($field, $lang = null)
    {
        return $this->$field;
    }
}