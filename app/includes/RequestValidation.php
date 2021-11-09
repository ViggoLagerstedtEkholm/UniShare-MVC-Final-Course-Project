<?php


namespace App\includes;

/**
 * Validate request data
 * @package App\includes
 * @author Viggo Lagerstedt Ekholm
 */
class RequestValidation
{
    /**
     * Validate credits
     * @param string $credits
     * @return bool
     */
    public static function validCredits(string $credits): bool
    {
        if (preg_match("/^[+]?([0-9]+\.?[0-9]*|\.[0-9]+)$/", $credits)) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }
}