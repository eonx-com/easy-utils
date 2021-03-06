<?php

declare(strict_types=1);

namespace EonX\EasyUtils\Tests;

use EonX\EasyUtils\Interfaces\MathInterface;

abstract class AbstractMathTestCase extends AbstractTestCase
{
    /**
     * @return mixed[]
     *
     * @see testAbsSucceeds
     */
    public function provideAbsData(): array
    {
        return [
            [
                'value' => '-10.4',
                'result' => '10.4',
                'precision' => 1,
            ],
            [
                'value' => '-10',
                'result' => '10',
            ],
            [
                'value' => '0.0',
                'result' => '0.0',
                'precision' => 1,
            ],
            [
                'value' => '10',
                'result' => '10',
            ],
            [
                'value' => '10.4',
                'result' => '10.4',
                'precision' => 1,
            ],
        ];
    }

    /**
     * @return mixed[]
     *
     * @see testCompSucceeds
     */
    public function provideCompData(): array
    {
        return [
            [
                'leftOperand' => '10000000',
                'rightOperand' => '10000001',
                'result' => -1,
            ],
            [
                'leftOperand' => '10000000',
                'rightOperand' => '10000000',
                'result' => 0,
            ],
            [
                'leftOperand' => '10000001',
                'rightOperand' => '10000000',
                'result' => 1,
            ],
        ];
    }

    /**
     * @return mixed[]
     *
     * @see testDivideSucceeds
     */
    public function provideDivideData(): array
    {
        return [
            'With null precision' => [
                'expected' => '333',
                'dividend' => '1000',
                'divisor' => '3',
                'precision' => null,
            ],
            'With precision' => [
                'expected' => '333.33',
                'dividend' => '1000',
                'divisor' => '3',
                'precision' => 2,
            ],
        ];
    }

    /**
     * @return mixed[]
     *
     * @see testRoundSucceeds
     */
    public function provideRoundData(): array
    {
        return [
            [
                'value' => '10.4',
                'expected' => '10',
            ],
            [
                'value' => '10.5',
                'expected' => '10',
            ],
            [
                'value' => '10.6',
                'expected' => '11',
            ],
            [
                'value' => '11.5',
                'expected' => '12',
            ],
            [
                'value' => '12.5',
                'expected' => '12',
            ],
            [
                'value' => '13.5',
                'expected' => '14',
            ],
        ];
    }

    /**
     * @dataProvider provideAbsData
     */
    public function testAbsSucceeds(string $value, string $result, ?int $precision = null): void
    {
        $math = $this->getMath();
        $actual = $math->abs($value, $precision);

        self::assertSame($result, $actual);
    }

    public function testAddSucceeds(): void
    {
        $math = $this->getMath();
        $actual = $math->add('10000000000000000000', '10000000000000000000');

        self::assertSame('20000000000000000000', $actual);
    }

    /**
     * @dataProvider provideCompData
     */
    public function testCompSucceeds(string $leftOperand, string $rightOperand, int $result): void
    {
        $math = $this->getMath();
        $actual = $math->comp($leftOperand, $rightOperand);

        self::assertSame($result, $actual);
    }

    /**
     * @dataProvider provideDivideData
     */
    public function testDivideSucceeds(
        string $expected,
        string $dividend,
        string $divisor,
        ?int $precision = null
    ): void {
        $math = $this->getMath();
        $actual = $math->divide($dividend, $divisor, $precision);

        self::assertSame($expected, $actual);
    }

    public function testMultiplySucceeds(): void
    {
        $math = $this->getMath();
        $actual = $math->multiply('10000000000000000000', '5');

        self::assertSame('50000000000000000000', $actual);
    }

    /**
     * @dataProvider provideRoundData
     */
    public function testRoundSucceeds(string $value, string $expected): void
    {
        $math = $this->getMath();
        $actual = $math->round($value);

        self::assertSame($expected, $actual);
    }

    public function testSubSucceeds(): void
    {
        $math = $this->getMath();
        $actual = $math->sub('20000000000000000000', '10000000000000000000');

        self::assertSame('10000000000000000000', $actual);
    }

    abstract protected function getMath(): MathInterface;
}
