<?php

declare(strict_types=1);

namespace Pollen\View\Exception;

use RuntimeException;
use Throwable;

class UnableCreateViewException extends RuntimeException
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        if (empty($message)) {
            $message = 'Unable to create view.';
        }

        parent::__construct($message, $code, $previous);
    }
}