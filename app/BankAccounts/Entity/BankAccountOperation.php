<?php

declare(strict_types=1);

namespace App\BankAccounts\Entity;

use App\SharedContext\Currency;
use App\SharedContext\OperationType;
use DateTime;

readonly class BankAccountOperation
{
    public function __construct(
        private ?string       $operationId,
        private OperationType $operationType,
        private Currency      $currency,
        private float         $amount,
        private float         $operationCost,
        private float         $operationFactor,
        private float         $amountWithCost,
        private DateTime      $operationDate,
    ) {
    }

    public function getOperationId(): ?string
    {
        return $this->operationId;
    }

    public function getOperationType(): OperationType
    {
        return $this->operationType;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getOperationCost(): float
    {
        return $this->operationCost;
    }

    public function getOperationFactor(): float
    {
        return $this->operationFactor;
    }

    public function getAmountWithCost(): float
    {
        return $this->amountWithCost;
    }

    public function getOperationDate(): DateTime
    {
        return $this->operationDate;
    }
}
