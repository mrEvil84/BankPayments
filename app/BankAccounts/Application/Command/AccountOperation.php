<?php

declare(strict_types=1);

namespace App\BankAccounts\Application\Command;

use App\BankAccounts\Entity\BankAccount;
use App\SharedContext\Currency;
use App\SharedContext\OperationType;
use DateTime;

abstract readonly class AccountOperation
{
    public function __construct(
        private OperationType $operationType,
        private BankAccount $account,
        private float $amount,
        private Currency $currency,
        private DateTime $operationDate,
    ) {
    }

    public function getOperationType(): OperationType
    {
        return $this->operationType;
    }

    public function getAccount(): BankAccount
    {
        return $this->account;
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