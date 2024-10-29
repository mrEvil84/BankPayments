<?php

declare(strict_types=1);

namespace App\BankAccounts\Application\Service\Exceptions;

use App\BankAccounts\SharedContext\OperationType;
use DomainException;

class InvalidOperationType extends DomainException
{
    public static function create(OperationType $operationType): self
    {
        return new self('Invalid operation type: ' . $operationType->type());
    }
}
