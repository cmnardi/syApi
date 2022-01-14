<?php

namespace App\Exception;

use Symfony\Component\Routing\Exception\ExceptionInterface;

/**
 * Class AccesDeniedException
 * @package Exception
 */
class AccessDeniedException extends \RuntimeException implements ExceptionInterface
{
}
