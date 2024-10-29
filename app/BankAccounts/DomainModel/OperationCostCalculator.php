<?php

declare(strict_types=1);

namespace App\BankAccounts\DomainModel;

interface OperationCostCalculator
{
    public const float CREDIT_OPERATION_FACTOR = 0.0;
    public const float DEBIT_OPERATION_FACTOR = 0.05;

    public function getCreditOperationCost(float $amount): float;
    public function getDebitOperationCost(float $amount): float;
}
