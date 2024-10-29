<?php

declare(strict_types=1);

namespace App\BankAccounts\Application\Service\Handlers;

use App\BankAccounts\Application\Command\AccountOperation;
use App\BankAccounts\Application\Command\Debit;
use App\BankAccounts\Application\Service\Exceptions\InsufficientFunds;
use App\BankAccounts\Application\Service\Exceptions\OperationsLimit;
use App\BankAccounts\DomainModel\BalanceCalculator;
use App\BankAccounts\DomainModel\BankAccountRepository;
use App\BankAccounts\DomainModel\OperationCostCalculator;
use App\BankAccounts\Entity\BankAccountOperation;
use App\BankAccounts\SharedContext\IdProvider;
use App\BankAccounts\SharedContext\OperationType;

class DebitOperationsHandler extends OperationsHandler
{
    public const int MAX_OPERATIONS_COUNT_PER_DAY = 3;

    public function __construct(
        private readonly BankAccountRepository   $repository,
        private readonly IdProvider              $idProvider,
        private readonly OperationCostCalculator $costCalculator,
        private readonly BalanceCalculator       $balanceCalculator,
    ) {
    }

    public function handle(AccountOperation $command): void
    {
        $this->assertOperationType($command, OperationType::DEBIT);
        $this->assertSameCurrency($command);
        $this->assertAmount($command);

        $this->assertBalanceSufficient($command);
        $this->assertDebitLimit($command);

        $backAccountOperation = new BankAccountOperation(
            $this->idProvider->getId(),
            $command->getOperationType(),
            $command->getCurrency(),
            $command->getAmount(),
            $this->costCalculator->getDebitOperationCost($command->getAmount()),
            OperationCostCalculator::DEBIT_OPERATION_FACTOR,
            $command->getAmount() + $this->costCalculator->getDebitOperationCost($command->getAmount()),
            $command->getOperationDate()
        );

        $this->repository->addBankAccountOperation($backAccountOperation);
    }

    /**
     * @throws InsufficientFunds
     */
    private function assertBalanceSufficient(AccountOperation|Debit $command): void
    {
        $amountWithCost = $this->costCalculator->getDebitOperationCost($command->getAmount()) + $command->getAmount();
        $operations = $this->repository->getBankAccountOperations($command->getBankAccount());
        $balance = $this->balanceCalculator->getBalance($operations);

        if ($balance < $amountWithCost) {
            throw InsufficientFunds::create($command->getOperationType());
        }
    }

    /**
     * @throws OperationsLimit
     */
    private function assertDebitLimit(AccountOperation|Debit $command): void
    {
        $operationsCount = $this->repository->getOperationsCountByDate(
            $command->getBankAccount(),
            $command->getOperationDate()
        );
        if ($operationsCount >= self::MAX_OPERATIONS_COUNT_PER_DAY) {
            throw OperationsLimit::create($command->getOperationType());
        }
    }
}
