<?php

declare(strict_types=1);

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crakter\BringApi\Entity;

use Crakter\BringApi\DefaultData\ValidateParameters;
use Crakter\BringApi\Exception\ApiEntityNotCorrectException;
use ReflectionObject;
use ReflectionProperty;

/**
 * BringApi ApiEntityBase
 *
 * A facility for Entity classes to be extended from
 *
 * Quick setup: <code>class ReportsEntity extends ApiEntityBase {}</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 * @deprecated since 4.0. Replaced by typed request DTOs under
 *             Bring\Api\Endpoint\<Api>\*Request. Kept for v3 compatibility.
 */
abstract class ApiEntityBase
{
    /**
     * @param string $validateParameters Validation parameters
     */
    protected $validateParameters = [];

    /**
     * @param string $xmlRootName Set the Name of Root XML
     */
    protected $xmlRootName = 'Entity';

    /**
     * Sets the validateParameter.
     */
    protected function setValidateParameter(string $name, string $value): ApiEntityInterface
    {
        $this->validateParameters[$name] = $value;

        return $this;
    }

    /**
     * Gets the validateParameters
     */
    public function getValidateParameters(): array
    {
        return $this->validateParameters;
    }

    /**
     * Sets the required Parameters, if you need extra checking.
     * @param  array              $parameters list of required variables from ListAvailableReportsCustomer
     */
    public function setRequiredParameters(array $parameters): ApiEntityInterface
    {
        foreach ($parameters as $name) {
            $this->setValidateParameter($name, ValidateParameters::NOT_NULL);
        }

        return $this;
    }

    /**
     * Check if values are according to validateParameters and all values must have a value, cannot be empty
     */
    private function checkValues(array $result): bool
    {
        if ($this->getValidateParameters() === []) {
            return true;
        }
        $checked = [];
        foreach ($this->validateParameters as $key => $valid) {
            foreach ($result as $k => $v) {
                $checked[] = $k;
                if (is_array($v)) {
                    continue;
                }
                if (isset($v) && $v !== null && $v !== '') {
                    continue;
                }
                if ($valid !== ValidateParameters::NOT_NULL) {
                    continue;
                }
                if ($k !== $key) {
                    continue;
                }
                throw new ApiEntityNotCorrectException(sprintf('%s is not allowed to be empty in %s', $k, static::class));
            }
            if (!in_array($key, $checked, true)) {
                throw new ApiEntityNotCorrectException(sprintf('%s must be set in %s', $key, static::class));
            }
        }

        return true;
    }

    /**
     * toArray returnes the object at hand in form of an Array
     * @see ApiEntityBase::checkValues();
     */
    public function toArray(): array
    {
        $result = [];
        $props = (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($props as $prop) {
            $name = $prop->getName();
            if (!$prop->isInitialized($this)) {
                continue;
            }
            $value = $prop->getValue($this);
            if ($value === null) {
                continue;
            }
            $result[$name] = $value;
        }
        $this->checkValues($result);

        return $result;
    }

    /**
     * toXml returnes the object at hand in form of an xml string
     */
    public function toXml(): string
    {
        $xml = new \SimpleXMLElement("<{$this->xmlRootName}/>");
        $result = $this->toArray();
        $this->recursiveXml($xml, $result);

        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());

        return $dom->saveXML();
    }

    /**
     * Recursive XML function to loop through all values.
     */
    private function recursiveXml(\SimpleXMLElement $object, array $data): bool
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $new_object = $object->addChild((string) $key);
                $this->recursiveXml($new_object, $value);
            } else {
                $escaped = htmlspecialchars((string) ($value ?? ''), ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $object->addChild((string) $key, $escaped);
            }
        }

        return true;
    }

    /**
     * Sets variables in class to be used by toArray()
     */
    public function set(array $entries): ApiEntityInterface
    {
        foreach ($entries as $key => $var) {
            $this->{$key} = $var;
        }

        return $this;
    }

    /**
     * Sets automagically values in class
     */
    public function __set(string $name, mixed $value): void
    {
        $this->{$name} = $value;
    }

    /**
     * Gets automagically values in class
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->{$name};
    }

    /**
     * We don't know which variables for the reports API that needs to be set as it's different on each report
     * This function settles it so you can use ->setFromDate('01.01.2017');
     * @param  string                          $name  name of the variable needed or you want to set
     * @param  array                           $value value that needs to be set
     * @return string|array|ApiEntityInterface
     */
    public function __call(string $name, array $value)
    {
        $var = lcfirst(substr($name, 3));

        if (strncasecmp($name, 'get', 3) === 0) {
            return $this->{$var} ?? '';
        }
        if (strncasecmp($name, 'set', 3) === 0) {
            $this->{$var} = $value[0];
        }

        return $this;
    }
}
