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

use ReflectionObject;
use ReflectionProperty;
use Crakter\BringApi\DefaultData\ValidateParameters;
use Crakter\BringApi\Exception\ApiEntityNotCorrectException;

/**
 * BringApi ApiEntityBase
 *
 * A facility for Entity classes to be extended from
 *
 * Quick setup: <code>class ReportsEntity extends ApiEntityBase {}</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
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
     * @param string $name
     * @param string $value
     */
    protected function setValidateParameter(string $name, string $value): ApiEntityInterface
    {
        $this->validateParameters[$name] = $value;

        return $this;
    }

    /**
     * Gets the validateParameters
     * @return array
     */
    public function getValidateParameters(): array
    {
        return $this->validateParameters;
    }

    /**
     * Sets the required Parameters, if you need extra checking.
     * @param  array              $parameters list of required variables from ListAvailableReportsCustomer
     * @return ApiEntityInterface
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
     * @param array $result
     */
    private function checkValues(array $result): bool
    {
        if (!empty($this->getValidateParameters())) {
            foreach ($this->validateParameters as $key => $valid) {
                foreach ($result as $k => $v) {
                    $checked[] = $k;
                    // Do not check if these values is set as these should of been checked.
                    if (is_array($v)) {
                        continue;
                    } else {
                        if ((!isset($v) || $v === null) && $valid == ValidateParameters::NOT_NULL) {
                            echo $k."\r\n";
                            echo $v;
                            throw new ApiEntityNotCorrectException(sprintf('%s is not allowed to be empty in %s', $k, __CLASS__));
                        }
                    }
                }
                if (!in_array($key, $checked)) {
                    throw new ApiEntityNotCorrectException(sprintf('%s must be set in %s', $key, __CLASS__));
                }
            }
        }

        return true;
    }

    /**
     * toArray returnes the object at hand in form of an Array
     * @see ApiEntityBase::checkValues();
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        $props = (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($props as $prop) {
            $name = $prop->getName();
            $value = $prop->getValue($this);
            $result[$name] = $value;
            $this->setValidateParameter($name, ValidateParameters::NOT_NULL);
        }
        $this->checkValues($result);

        return $result;
    }

    /**
     * toXml returnes the object at hand in form of an xml string
     * @return string
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
     * @param  SimpleXMLElement $object
     * @param  array            $data
     * @return void
     */
    private function recursiveXml(\SimpleXMLElement $object, array $data): bool
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $new_object = $object->addChild($key);
                $this->recursiveXml($new_object, $value);
            } else {
                $object->addChild($key, $value);
            }
        }

        return true;
    }

    /**
     * Sets variables in class to be used by toArray()
     * @param  array              $entries
     * @return ApiEntityInterface
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
     * @param  string             $name
     * @param  mixed              $value
     * @return ApiEntityInterface
     */
    public function __set(string $name, $value): ApiEntityInterface
    {
        $this->{$name} = $value;

        return $this;
    }

    /**
     * Gets automagically values in class
     * @param  string $name
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

        if (strncasecmp($name, "get", 3) === 0) {
            return $this->{$var} ?? '';
        }
        if (strncasecmp($name, "set", 3) === 0) {
            $this->{$var} = $value[0];
        }

        return $this;
    }
}
