<?php

declare(strict_types=1);

namespace App\BankAccounts\Application\Service\Handlers;

use App\BankAccounts\Application\Command\AccountOperation;
use App\BankAccounts\Application\Service\Exceptions\IncompatibleCurrencyType;
use App\BankAccounts\Application\Service\Exceptions\InvalidOperationAmount;
use App\BankAccounts\Application\Service\Exceptions\InvalidOperationType;
use App\BankAccounts\DomainModel\BankAccountRepository;
use App\BankAccounts\DomainModel\OperationCostCalculator;
use App\BankAccounts\Entity\BankAccountOperation;
use App\BankAccounts\SharedContext\IdProvider;
use App\BankAccounts\SharedContext\OperationType;

class CreditOperationHandler extends OperationsHandler
{
    public function __construct(
        private readonly BankAccountRepository   $repository,
        private readonly IdProvider              $idProvider,
        private readonly OperationCostCalculator $costCalculator,
    ) {
    }

    /**
     * @throws IncompatibleCurrencyType|InvalidOperationType|InvalidOperationAmount
     */
    public function handle(AccountOperation $command): void
    {
        $this->assertOperationType($command, OperationType::CREDIT);
        $this->assertSameCurrency($command);
        $this->assertAmount($command);

        $backAccountOperation = new BankAccountOperation(
            $this->idProvider->getId(),
            $command->getOperationType(),
            $command->getCurrency(),
            $command->getAmount(),
            $this->costCalculator->getCreditOperationCost($command->getAmount()),
            OperationCostCalculator::CREDIT_OPERATION_FACTOR,
            $command->getAmount(),
            $command->getOperationDate()
        );

        $this->repository->addBankAccountOperation($backAccountOperation);
    }
}
