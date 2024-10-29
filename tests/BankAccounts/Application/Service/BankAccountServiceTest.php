<?php

declare(strict_types=1);

namespace Tests\BankAccounts\Application\Service;

use App\BankAccounts\Application\Command\Credit;
use App\BankAccounts\Application\Service\BankAccountService;
use App\BankAccounts\Application\Service\Exceptions\IncompatibleCurrencyType;
use App\BankAccounts\Application\Service\Exceptions\InvalidOperationAmount;
use App\BankAccounts\Application\Service\Exceptions\InvalidOperationType;
use App\BankAccounts\DomainModel\BalanceCalculator;
use App\BankAccounts\DomainModel\BankAccountRepository;
use App\BankAccounts\DomainModel\OperationCostCalculator;
use App\BankAccounts\Entity\BankAccount;
use App\BankAccounts\Entity\BankAccountOperation;
use App\SharedContext\Currency;
use App\SharedContext\IdProvider;
use App\SharedContext\OperationType;
use DateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BankAccountServiceTest extends TestCase
{
    private BankAccountRepository|MockObject $repository;
    private IdProvider|MockObject $idProvider;
    private OperationCostCalculator|MockObject $operationCostCalculator;
    private BalanceCalculator|MockObject $balanceCalculator;


    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(BankAccountRepository::class);
        $this->idProvider = $this->createMock(IdProvider::class);
        $this->operationCostCalculator = $this->createMock(OperationCostCalculator::class);
        $this->balanceCalculator = $this->createMock(BalanceCalculator::class);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function shouldCredit(): void
    {
        $operationDate = new DateTime();
        $testId = 'abcd';

        $command = new Credit(
            OperationType::CREDIT,
            new BankAccount($testId, Currency::EUR),
            1000.00,
            Currency::EUR,
            $operationDate
        );

        $expectedEntity = new BankAccountOperation(
            $testId,
            OperationType::CREDIT,
            Currency::EUR,
            1000.00,
            0.0,
            0.0,
            1000.00,
            $operationDate
        );
        $this->repository->expects($this->once())->method('addBankAccountOperation')->with($expectedEntity);
        $this->idProvider->expects($this->once())->method('getId')->willReturn($testId);
        $this->operationCostCalculator->expects($this->once())->method('getCreditOperationCost')->willReturn(0.00);

        $sut = new BankAccountService(
            $this->repository,
            $this->idProvider,
            $this->operationCostCalculator,
            $this->balanceCalculator
        );
        $sut->credit($command);
    }

    #[DataProvider('getInvalidCurrencyTypedData')]
    #[Test]
    public function shouldThrowExceptionWhenCreditCurrencyIncompatible(Credit $command): void
    {
        $sut = new BankAccountService(
            $this->repository,
            $this->idProvider,
            $this->operationCostCalculator,
            $this->balanceCalculator
        );
        $this->expectException(IncompatibleCurrencyType::class);
        $sut->credit($command);
    }

    #[Test]
    public function shouldThrowExceptionWhenInvalidCreditOperationType(): void
    {
        $command = new Credit(
            OperationType::DEBIT,
            new BankAccount('id-abcd', Currency::PLN),
            1000.00,
            Currency::PLN,
            new DateTime()
        );

        $sut = new BankAccountService(
            $this->repository,
            $this->idProvider,
            $this->operationCostCalculator,
            $this->balanceCalculator
        );
        $this->expectException(InvalidOperationType::class);
        $sut->credit($command);
    }

    #[DataProvider('getInvalidCreditAmountData')]
    #[Test]
    public function shouldThrowExceptionWhenInvalidCreditOperationAmount(Credit $command): void
    {
        $sut = new BankAccountService(
            $this->repository,
            $this->idProvider,
            $this->operationCostCalculator,
            $this->balanceCalculator
        );
        $this->expectException(InvalidOperationAmount::class);
        $sut->credit($command);
    }

    public static function getInvalidCurrencyTypedData(): array
    {
        $testId = 'id-abcd';
        $operationDate = new DateTime();
        return [
            [
                new Credit(
                    OperationType::CREDIT,
                    new BankAccount($testId, Currency::PLN),
                    1000.00,
                    Currency::EUR,
                    $operationDate
                ),
            ],
            [
                new Credit(
                    OperationType::CREDIT,
                    new BankAccount($testId, Currency::EUR),
                    1000.00,
                    Currency::USD,
                    $operationDate
                ),
            ],
        ];
    }

    public static function getInvalidCreditAmountData(): array
    {
        $testId = 'id-abcd';
        $operationDate = new DateTime();
        return [
            [
                new Credit(
                    OperationType::CREDIT,
                    new BankAccount($testId, Currency::EUR),
                    0.00,
                    Currency::EUR,
                    $operationDate
                ),
            ],
            [
                new Credit(
                    OperationType::CREDIT,
                    new BankAccount($testId, Currency::EUR),
                    -2.00,
                    Currency::EUR,
                    $operationDate
                ),
            ],
        ];
    }
}
