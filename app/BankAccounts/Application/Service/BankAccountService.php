<?php

declare(strict_types=1);

namespace App\BankAccounts\Application\Service;

use App\BankAccounts\Application\Command\AccountOperation;
use App\BankAccounts\Application\Command\Credit;
use App\BankAccounts\Application\Command\Debit;
use App\BankAccounts\Application\Service\Exceptions\IncompatibleCurrencyType;
use App\BankAccounts\Application\Service\Exceptions\InvalidOperationAmount;
use App\BankAccounts\Application\Service\Exceptions\InvalidOperationType;
use App\BankAccounts\DomainModel\BalanceCalculator;
use App\BankAccounts\DomainModel\BankAccountRepository;
use App\BankAccounts\DomainModel\OperationCostCalculator;
use App\BankAccounts\Entity\BankAccountOperation;
use App\SharedContext\IdProvider;
use App\SharedContext\OperationType;

readonly class BankAccountService
{
    public function __construct(
        private BankAccountRepository $repository,
        private IdProvider $idProvider,
        private OperationCostCalculator $operationCostCalculator,
        private BalanceCalculator $balanceCalculator,
    ) {
    }

    /**
     * @throws IncompatibleCurrencyType|InvalidOperationType|InvalidOperationAmount
     */
    public function credit(Credit $command): void
    {
        $this->assertOperationType($command, OperationType::CREDIT);
        $this->assertSameCurrency($command);
        $this->assertAmount($command);

        $backAccountOperation = new BankAccountOperation(
            $this->idProvider->getId(),
            $command->getOperationType(),
            $command->getCurrency(),
            $command->getAmount(),
            $this->operationCostCalculator->getCreditOperationCost($command->getAmount()),
            OperationCostCalculator::CREDIT_OPERATION_FACTOR,
            $command->getAmount(),
            $command->getOperationDate()
        );

        $this->repository->addBankAccountOperation($backAccountOperation);
    }

    public function debit(Debit $command): void
    {
        $this->assertOperationType($command, OperationType::DEBIT);
        $this->assertSameCurrency($command);
        $this->assertAmount($command);
        $this->assertBallanceSufficient($command);


        $backAccountOperation = new BankAccountOperation(
            $this->idProvider->getId(),
            $command->getOperationType(),
            $command->getCurrency(),
            $command->getAmount(),
            $this->operationCostCalculator->getDebitOperationCost($command->getAmount()),
            OperationCostCalculator::DEBIT_OPERATION_FACTOR,
            $command->getAmount(),
            $command->getOperationDate()
        );

        $this->repository->addBankAccountOperation($backAccountOperation);
    }

    private function assertSameCurrency(AccountOperation $accountOperation): void
    {
        if ($accountOperation->getAccount()->getCurrency()->code() !== $accountOperation->getCurrency()->code()) {
            throw IncompatibleCurrencyType::create($accountOperation->getOperationType());
        }
    }

    private function assertAmount(AccountOperation $command): void
    {
        if ($command->getAmount() === 0.0 || $command->getAmount() < 0) {
            throw InvalidOperationAmount::create();
        }
    }

    private function assertOperationType(AccountOperation $command, OperationType $expectedOperationType): void
    {
        if ($command->getOperationType()->type() !== $expectedOperationType->type()) {
            throw InvalidOperationType::create($expectedOperationType);
        }
    }

    private function assertBallanceSufficient(Debit $command): void
    {
        $amountWithCost = $this->operationCostCalculator->getDebitOperationCost($command->getAmount());
        $ballance = $this->repository->getBankAccountOperations($command->getAccount());

    }
}
