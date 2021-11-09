<?php


namespace App\includes;

/**
 * Validate review data
 * @package App\includes
 * @author Viggo Lagerstedt Ekholm
 */
class ReviewValidator
{
    /**
     * Validate ratings
     * @param array $ratings
     * @return bool
     */
    public static function validRatings(array $ratings): bool
    {
        foreach($ratings as $value){
            if (!preg_match("/^([1-9]|10)$/", $value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validate text
     * @param string $text
     * @return bool
     */
    public static function validText(string $text): bool
    {
        if (preg_match("/^[\s\S]{200,5000}$/", $text)) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }
}