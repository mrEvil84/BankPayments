<?php

declare(strict_types=1);

namespace App\BankAccounts\Application\Service\Exceptions;

use DomainException;

class InvalidOperationAmount extends DomainException
{
    public static function create(): self
    {
        return new self('Invalid operation amount.');
    }
}
