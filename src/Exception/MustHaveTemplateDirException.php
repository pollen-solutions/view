<?php

declare(strict_types=1);

namespace Pollen\View\Exception;

use RuntimeException;
use Throwable;

class MustHaveTemplateDirException extends RuntimeException
{
    public function __construct(?string $engine = '', string $message = '', int $code = 0, Throwable $previous = null)
    {
        if (empty($message)) {
            $message = !empty($engine)
                ? sprintf('Template directory is required by [%s]. Use View setDirectory method.', $engine)
                : 'Template directory is required. Use View setDirectory method.';
        }

        parent::__construct($message, $code, $previous);
    }
}