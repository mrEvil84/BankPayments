<?php

declare(strict_types=1);

namespace Tests\BankAccounts\Application\Service;

use App\BankAccounts\Application\Service\Calculators\BankAccountOperationCostCalculator;
use App\BankAccounts\Application\Service\Exceptions\InvalidOperationAmount;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BankAccountOperationCostCalculatorTest extends TestCase
{
    private BankAccountOperationCostCalculator $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new BankAccountOperationCostCalculator();
    }

    #[DataProvider('getCorrectCreditCostAmounts')]
    #[Test]
    public function shouldGetCreditOperationCost(float $amount, float $expectedCost): void
    {
        $actualCost = $this->sut->getCreditOperationCost($amount);
        $this->assertSame($expectedCost, $actualCost);
    }

    #[Test]
    public function shouldThrowExceptionWhenAmountIsInvalid(): void
    {
        $this->expectException(InvalidOperationAmount::class);
        $this->sut->getCreditOperationCost(0.00);
        $this->sut->getCreditOperationCost(-1.00);
    }

    #[DataProvider('getCorrectDebitCostAmounts')]
    #[Test]
    public function shouldGetDebitOperationsCost(float $amount, float $expectedCost): void
    {
        $actualCost = $this->sut->getDebitOperationCost($amount);
        $this->assertSame($expectedCost, $actualCost);
    }

    public static function getCorrectCreditCostAmounts(): array
    {
        return [
            [
                1000.00,
                0.00
            ],
            [
                1.00,
                0.00,
            ]
        ];
    }

    public static function getCorrectDebitCostAmounts(): array
    {
        return [
            [
                1000.00,
                50.00
            ],
            [
                1.00,
                0.05
            ],
            [
                500.00,
                25.00
            ],
        ];
    }
}
