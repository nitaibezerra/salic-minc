<?php
class TratarString
{
    public static function escapeString($string)
    {
        $string = str_replace("'", "''", $string);
        return $string;
    }

    public static function criarSlug($string)
    {
        $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
        return $slug;
    }
}