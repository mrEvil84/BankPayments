<?php

declare(strict_types=1);

namespace App\BankAccounts\Infrastructure;

use App\BankAccounts\DomainModel\BankAccountRepository;
use App\BankAccounts\Entity\BankAccount;
use App\BankAccounts\Entity\BankAccountOperation;
use App\BankAccounts\Entity\BankAccountOperationCollection;
use DateTime;

class BankAccountStorage implements BankAccountRepository
{
    public function addBankAccountOperation(BankAccountOperation $bankAccountOperation): void
    {
        json_encode([
            'id' => $bankAccountOperation->getOperationId(),
            'operationType' => $bankAccountOperation->getOperationType()->type(),
            'currency' => $bankAccountOperation->getCurrency()->code(),
            'amount' => $bankAccountOperation->getAmount(),
            'operationCost' => $bankAccountOperation->getOperationCost(),
            'amountWithCost' => $bankAccountOperation->getAmountWithCost(),
            'operationDate' => $bankAccountOperation->getOperationDate()->format(DATE_ATOM),

        ]);
        // store json in db or send to api or create event or sent to queue system
    }


    public function getDebitOperationsCountAtDate(DateTime $date): int
    {
        return 1;
    }

    public function getBankAccountOperations(BankAccount $account): BankAccountOperationCollection
    {
        return new BankAccountOperationCollection();
    }

    public function getOperationsCountByDate(BankAccount $account, DateTime $date): int
    {
        return 1;
    }
}
