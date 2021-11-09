<?php


namespace App\includes;

/**
 * Validate project data
 * @package App\includes
 * @author Viggo Lagerstedt Ekholm
 */
class ProjectValidator
{
    /**
     * This method checks if a valid link has been uploaded by the user.
     * @param string $link
     * @return bool
     */
    public static function validURL(string $link): bool
    {
        if (filter_var($link, FILTER_VALIDATE_URL)) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }
}