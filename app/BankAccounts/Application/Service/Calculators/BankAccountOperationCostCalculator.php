<?php

declare(strict_types=1);

namespace App\BankAccounts\Application\Service\Calculators;

use App\BankAccounts\Application\Service\Exceptions\InvalidOperationAmount;
use App\BankAccounts\DomainModel\OperationCostCalculator;

class BankAccountOperationCostCalculator implements OperationCostCalculator
{
    public function getCreditOperationCost(float $amount): float
    {
        $this->assertAmount($amount);
        return $amount * self::CREDIT_OPERATION_FACTOR;
    }

    public function getDebitOperationCost(float $amount): float
    {
        $this->assertAmount($amount);
        return $amount * self::DEBIT_OPERATION_FACTOR;
    }

    private function assertAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw InvalidOperationAmount::create();
        }
    }
}