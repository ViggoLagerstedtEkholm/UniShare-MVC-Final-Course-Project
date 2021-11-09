<?php
namespace App\core\Exceptions;
use \Exception;

/**
 * Exception if image generation fails.
 * @package App\core\Exceptions
 * @author Viggo Lagerstedt Ekholm
 */
class GDResizeException extends Exception{
  protected $message = 'Failed to resize image for either profile picture or project picture.';
  protected $code = 500;
}
