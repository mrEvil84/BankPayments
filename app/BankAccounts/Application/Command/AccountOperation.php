<?php

declare(strict_types=1);

namespace App\BankAccounts\Application\Command;

use App\BankAccounts\Entity\BankAccount;
use App\BankAccounts\SharedContext\Currency;
use App\BankAccounts\SharedContext\OperationType;
use DateTime;

abstract readonly class AccountOperation
{
    public function __construct(
        private OperationType $operationType,
        private BankAccount   $bankAccount,
        private float         $amount,
        private Currency      $currency,
        private DateTime      $operationDate,
    ) {
    }

    public function getOperationType(): OperationType
    {
        return $this->operationType;
    }

    public function getBankAccount(): BankAccount
    {
        return $this->bankAccount;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getOperationDate(): DateTime
    {
        return $this->operationDate;
    }
}
