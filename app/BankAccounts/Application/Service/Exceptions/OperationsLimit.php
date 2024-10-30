<?php

declare(strict_types=1);

namespace App\BankAccounts\Application\Service\Exceptions;

use App\BankAccounts\SharedContext\OperationType;
use DomainException;

class OperationsLimit extends DomainException
{
    public static function create(OperationType $operationType): self
    {
        return new self('Operations limit reached for ' . $operationType->type());
    }
}
