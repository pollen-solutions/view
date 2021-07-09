<?php

declare(strict_types=1);

namespace Pollen\View\Exception;

use RuntimeException;
use Throwable;

class UnableCreateEngineException extends RuntimeException
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        if (empty($message)) {
            $message = 'Unable to create view engine';
        }

        parent::__construct($message, $code, $previous);
    }
}