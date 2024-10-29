<?php

declare(strict_types=1);

namespace App\BankAccounts\Application\Service\Calculators;

use App\BankAccounts\DomainModel\BalanceCalculator;
use App\BankAccounts\Entity\BankAccountOperation;
use App\BankAccounts\Entity\BankAccountOperationCollection;
use App\SharedContext\OperationType;

class BankAccountOperationsBalanceCalculator implements BalanceCalculator
{
    public function getBalance(BankAccountOperationCollection $accountOperations): float
    {
        $balance = 0.0;

        /** @var BankAccountOperation $accountOperation */
        foreach ($accountOperations as $accountOperation) {
            if ($accountOperation->getOperationType() === OperationType::CREDIT) {
                $balance = $balance + $accountOperation->getAmountWithCost();
            }

            if ($accountOperation->getOperationType() === OperationType::DEBIT) {
                $balance = $balance - ($accountOperation->getAmount() + $accountOperation->getOperationCost());
            }
        }

        return $balance;
    }
}
