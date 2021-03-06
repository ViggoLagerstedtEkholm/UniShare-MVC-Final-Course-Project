<?php


namespace App\includes;

/**
 * Validate user data
 * Class UserValidation
 * @package App\includes
 * @author Viggo Lagerstedt Ekholm
 */
class UserValidation
{
    /**
     * This method checks if a valid email has been uploaded by the user.
     * @param string $email
     * @return bool
     */
    public static function validEmail(string $email): bool
    {
        if (preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $email))
        {
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }

    /**
     * Validate password
     * @param string $password
     * @return bool
     */
    public static function validPassword(string $password): bool
    {
        //Minimum eight and maximum 80 characters, at least one uppercase letter, one lowercase letter, one number and one special character.
        if(preg_match("/^.{8,80}$/", $password)){
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }

    /**
     * Validate first name
     * @param string $password
     * @return bool
     */
    public static function validFirstname(string $password): bool
    {
        //[A-Za-z ]{1,50} will check for the characters and length, while the negative lookahead (?!.*?\s{2})
        // will check for the spaces condition. (\b) to disallow white space at ends.
        if(preg_match("/^(?=.{2,30}$)[a-zA-Z\x{00C0}-\x{00ff}]+(?:[-'\s][a-zA-Z\x{00C0}-\x{00ff}]+)*$/", $password)){
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }

    /**
     * Validate last name
     * @param string $password
     * @return bool
     */
    public static function validLastname(string $password): bool
    {
        //[A-Za-z ]{1,50} will check for the characters and length, while the negative lookahead (?!.*?\s{2})
        // will check for the spaces condition. (\b) to disallow white space at ends.
        if(preg_match("/^(?=.{2,30}$)[a-zA-Z\x{00C0}-\x{00ff}]+(?:[-'\s][a-zA-Z\x{00C0}-\x{00ff}]+)*$/", $password)){
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }

    /**
     * This method checks if a valid username has been uploaded by the user.
     * @param string $username
     * @return bool
     */
    public static function validUsername(string $username): bool
    {
        //a-z and A-Z minimum 5 characters and maximum 30.
        if (preg_match("/^[a-zA-Z0-9]{8,30}$/", $username)) {
            $result = true;
        } else {
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
        if (preg_match("/^.{0,500}$/", $description))
        {
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }

    /**
     * Validate match
     * @param string $password
     * @param string $password_repeat
     * @return bool
     */
    public static function match(string $password, string $password_repeat): bool
    {
        if ($password === $password_repeat) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }
}