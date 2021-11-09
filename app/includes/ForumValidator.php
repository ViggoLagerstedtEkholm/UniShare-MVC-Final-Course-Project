<?php


namespace App\includes;

/**
 * Validate forum data
 * @package App\includes
 * @author Viggo Lagerstedt Ekholm
 */
class ForumValidator
{
    /**
     * Validate title
     * @param string $text
     * @return bool
     */
    public static function validTitle(string $text): bool
    {
        if (preg_match("/^.{5,50}$/", $text))
        {
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }

    /**
     * Validate topic
     * @param string $text
     * @return bool
     */
    public static function validTopic(string $text): bool
    {
        if (preg_match("/^.{1,50}$/", $text))
        {
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }
}