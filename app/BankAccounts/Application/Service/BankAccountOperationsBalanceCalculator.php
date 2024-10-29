<?php

declare(strict_types=1);

namespace App\BankAccounts\Application\Service;

use App\BankAccounts\DomainModel\BalanceCalculator;
use App\BankAccounts\Entity\BankAccountOperation;
use App\SharedContext\OperationType;

class BankAccountOperationsBalanceCalculator implements BalanceCalculator
{
    public function getBalance(array $accountOperations): float
    {
        $balance = 0.0;
        /** @var BankAccountOperation $accountOperation */
        foreach ($accountOperations as $accountOperation) {
            if ($accountOperation->getOperationType() === OperationType::CREDIT) {
                $balance = $balance + $accountOperation->getAmount();
            }

            if ($accountOperation->getOperationType() === OperationType::DEBIT) {
                $balance = $balance - $accountOperation->getAmount() - $accountOperation->getOperationCost();
            }
        }

        return $balance;
    }
}
