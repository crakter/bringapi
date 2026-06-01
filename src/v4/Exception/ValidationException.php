<?php

declare(strict_types=1);

namespace Bring\Api\Exception;

final class ValidationException extends \RuntimeException implements BringException
{
    /** @param list<string> $violations */
    public function __construct(string $message, private readonly array $violations = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    /** @return list<string> */
    public function getViolations(): array
    {
        return $this->violations;
    }
}
