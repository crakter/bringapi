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

    public function setUp()
    {
        $this->class = new EntityTesting();
    }

    public function testGetValidationParameters()
    {
        $this->class->setRequiredParameters(['name']);
        $this->assertSame($this->class->getValidateParameters(), ['name' => ValidateParameters::NOT_NULL]);
    }

    public function testSetRequiredParameters()
    {
        $this->class->setRequiredParameters(['name']);
        $this->assertSame($this->class->getValidateParameters(), ['name' => ValidateParameters::NOT_NULL]);
    }

    public function testCheckValuesException()
    {
        $this->expectException(ApiEntityNotCorrectException::class);
        $this->class->setRequiredParameters(['name']);
        $this->class->toArray();
    }

    public function testCheckValues()
    {
        $this->class->setQ('testing');
        $this->assertArrayHasKey('q', $this->class->toArray());
    }

    public function testToArray()
    {
        $this->class->setQ('testing');
        $this->assertSame($this->class->toArray(), ['q' => 'testing']);
    }

    public function testToXml()
    {
        $this->class->setQ('testing');
        $this->class->setP(['test' => 'x']);
        $this->assertSame(str_replace(["\r", "\n", '  '], '', $this->class->toXml()), '<?xml version="1.0"?><Entity><q>testing</q><p><test>x</test></p></Entity>');
    }

    public function testSet()
    {
        $this->class->set(['q' => 'testing']);
        $this->assertSame($this->class->toArray(), ['q' => 'testing']);
    }

    public function testAutomaticSet()
    {
        $this->class->q = 'testing';
        $this->assertSame($this->class->toArray(), ['q' => 'testing']);
    }

    public function testAutomaticGet()
    {
        $this->class->q = 'testing';
        $this->assertSame('testing', $this->class->q);
    }

    public function testAutomaticCallGet()
    {
        $this->class->q = 'testing';
        $this->assertSame('testing', $this->class->getQ());
    }

    public function testAutomaticCallSet()
    {
        $this->class->setQ('testing');
        $this->assertSame('testing', $this->class->getQ());
    }
}

class EntityTesting extends ApiEntityBase implements ApiEntityInterface
{
    public $q;
}
