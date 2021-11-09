<?php
namespace App\core\Exceptions;
use \Exception;

/**
 * Exception if user has insufficient privileges
 * @package App\core\Exceptions
 * @author Viggo Lagerstedt Ekholm
 */
class PrivilegeException extends Exception{
  protected $message = 'You do not have the privileges to access this page';
  protected $code = 401;
}
