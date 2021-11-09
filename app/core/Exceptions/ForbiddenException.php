<?php
namespace App\core\Exceptions;
use \Exception;

/**
 * Exception if the user is not authorized.
 * @package App\core\Exceptions
 * @author Viggo Lagerstedt Ekholm
 */
class ForbiddenException extends Exception{
  protected $message = 'You need to be logged in to do this!';
  protected $code = 403;
}
