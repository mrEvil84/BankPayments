<?php

declare(strict_types=1);

namespace Tests\BankAccounts\Application\Service\Handlers;

use App\BankAccounts\DomainModel\BalanceCalculator;
use App\BankAccounts\DomainModel\BankAccountRepository;
use App\BankAccounts\DomainModel\OperationCostCalculator;
use App\SharedContext\IdProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class HandlerBaseTestCase extends TestCase
{
    public BankAccountRepository|MockObject $repository;
    public IdProvider|MockObject $idProvider;
    public OperationCostCalculator|MockObject $costCalculator;
    public BalanceCalculator|MockObject $balanceCalculator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(BankAccountRepository::class);
        $this->idProvider = $this->createMock(IdProvider::class);
        $this->costCalculator = $this->createMock(OperationCostCalculator::class);
        $this->balanceCalculator = $this->createMock(BalanceCalculator::class);
    }
}
