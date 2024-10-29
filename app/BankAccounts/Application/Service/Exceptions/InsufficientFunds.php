<?php

declare(strict_types=1);

namespace App\BankAccounts\Application\Service\Exceptions;

use App\SharedContext\OperationType;
use DomainException;

class InsufficientFunds extends DomainException
{
    public static function create(OperationType $operationType): self
    {
        return new self('Insufficient funds for ' . $operationType->type());
    }
}
