<?php


namespace App\includes;

/**
 * Validate shared data
 * @package App\includes
 * @author Viggo Lagerstedt Ekholm
 */
class SharedValidation
{
    /**
     * Validate country
     * @param string $country
     * @return bool
     */
    public static function validCountry(string $country): bool
    {
        if (preg_match("/^.{1,56}$/", $country))
        {
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }

    /**
     * Validate city
     * @param string $city
     * @return bool
     */
    public static function validCity(string $city): bool
    {
        if (preg_match("/^.{1,120}$/", $city))
        {
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }

    /**
     * Validate university
     * @param string $university
     * @return bool
     */
    public static function validUniversity(string $university): bool
    {
        if (preg_match("/^.{1,100}$/", $university))
        {
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }

    /**
     * Validate description
     * @param string $description
     * @return bool
     */
    public static function validDescription(string $description): bool
    {
        if (preg_match("/^.{5,2000}$/", $description))
        {
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }

    /**
     * Validate name
     * @param string $name
     * @return bool
     */
    public static function validName (string $name): bool
    {
        if (preg_match("/^(?=.{1,150}$)[a-zA-Z\x{00C0}-\x{00ff}]+(?:[-'\s][a-zA-Z\x{00C0}-\x{00ff}]+)*$/", $name))
        {
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }
}