<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class ValidationException extends RuntimeException
{
    /** @param array<string,string> $errors field => first message */
    public function __construct(public array $errors)
    {
        parent::__construct('The given data was invalid.');
    }
}
