<?php


namespace App\includes;

/**
 * Validate post data
 * @package App\includes
 * @author Viggo Lagerstedt Ekholm
 */
class PostValidator
{
    /**
     * Validate post
     * @param string $text
     * @return bool
     */
    public static function validPost(string $text): bool
    {
        if (preg_match("/^.{5,500}$/", $text))
        {
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }
}