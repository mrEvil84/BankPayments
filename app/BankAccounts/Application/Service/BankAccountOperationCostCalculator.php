<?php

declare(strict_types=1);

namespace App\BankAccounts\Application\Service;

use App\BankAccounts\DomainModel\OperationCostCalculator;

class BankAccountOperationCostCalculator implements OperationCostCalculator
{
    public function getCreditOperationCost(float $amount): float
    {
        return $amount * self::CREDIT_OPERATION_FACTOR;
    }

    public function getDebitOperationCost(float $amount): float
    {
        return $amount * self::DEBIT_OPERATION_FACTOR;
    }
}