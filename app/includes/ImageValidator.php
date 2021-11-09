<?php


namespace App\includes;

/**
 * Validate image data
 * @package App\includes
 * @author Viggo Lagerstedt Ekholm
 */
class ImageValidator
{
    /**
     * This method checks if a valid image extension has been uploaded by the user.
     * @param string $global
     * @return bool
     */
    public static function hasValidImageExtension(string $global): bool
    {
        $result = false;
        if(isset( $_FILES[$global])){
            $fileType = $_FILES[$global]['type'];

            $allowed = array("image/jpeg", "image/gif", "image/png");
            if (in_array($fileType, $allowed)) {
                $result = true;
            } else {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * This method checks if a file has been uploaded by the user.
     * @param mixed
     * @return bool
     */
    public static function hasValidUpload(string $global): bool
    {
        if(isset($_FILES[$global])){
            $file = $_FILES[$global]['tmp_name'];
            if (!file_exists($file) || !is_uploaded_file($file)) {
                return false;
            }
        }

        return true;
    }
}