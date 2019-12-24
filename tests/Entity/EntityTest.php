<?php

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crakter\BringApi\Entity;

use PHPUnit\Framework\TestCase;
use Crakter\BringApi\DefaultData\ValidateParameters;
use Crakter\BringApi\Exception\ApiEntityNotCorrectException;

class EntityTest extends TestCase
{
    private $class;

    public function setUp(): void
    {
        $this->class = new EntityTesting();
    }

    public function testGetValidationParameters(): void
    {
        $this->class->setRequiredParameters(['name']);
        $this->assertSame($this->class->getValidateParameters(), ['name' => ValidateParameters::NOT_NULL]);
    }

    public function testSetRequiredParameters(): void
    {
        $this->class->setRequiredParameters(['name']);
        $this->assertSame($this->class->getValidateParameters(), ['name' => ValidateParameters::NOT_NULL]);
    }

    public function testCheckValuesException(): void
    {
        $this->expectException(ApiEntityNotCorrectException::class);
        $this->class->setRequiredParameters(['name']);
        $this->class->toArray();
    }

    public function testCheckValues(): void
    {
        $this->class->setQ('testing');
        $this->assertArrayHasKey('q', $this->class->toArray());
    }

    public function testToArray(): void
    {
        $this->class->setQ('testing');
        $this->assertSame($this->class->toArray(), ['q' => 'testing']);
    }

    public function testToXml(): void
    {
        $this->class->setQ('testing');
        $this->class->setP(['test' => 'x']);
        $this->assertSame(str_replace(["\r", "\n", '  '], '', $this->class->toXml()), '<?xml version="1.0"?><Entity><q>testing</q><p><test>x</test></p></Entity>');
    }

    public function testSet(): void
    {
        $this->class->set(['q' => 'testing']);
        $this->assertSame($this->class->toArray(), ['q' => 'testing']);
    }

    public function testAutomaticSet(): void
    {
        $this->class->q = 'testing';
        $this->assertSame($this->class->toArray(), ['q' => 'testing']);
    }

    public function testAutomaticGet(): void
    {
        $this->class->q = 'testing';
        $this->assertSame('testing', $this->class->q);
    }

    public function testAutomaticCallGet(): void
    {
        $this->class->q = 'testing';
        $this->assertSame('testing', $this->class->getQ());
    }

    public function testAutomaticCallSet(): void
    {
        $this->class->setQ('testing');
        $this->assertSame('testing', $this->class->getQ());
    }
}

class EntityTesting extends ApiEntityBase implements ApiEntityInterface
{
    public $q;
}
