<?php
/**
 * Pactum: Config manager
 * Copyright (c) NewClass (http://newclass.pl)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the file LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) NewClass (http://newclass.pl)
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Pactum;

/**
 * Config builder.
 * @package Pactum
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class ConfigBuilderObject
{

    /**
     *
     * @var ConfigBuilderValue[]
     */
    private $values = [];

    /**
     *
     * @var ConfigBuilderObject[]
     */
    private $objects = [];

    /**
     * @var ConfigBuilderArray[]
     */
    private $arrays = [];
    /**
     * @var bool
     */
    private $required;

    /**
     * ConfigBuilderObject constructor.
     * @param bool $required
     */
    public function __construct($required=true)
    {
        $this->required = $required;
    }

    /**
     *
     * @param string $name
     * @param string $default
     * @return $this
     */
    public function addString($name, $default = null)
    {
        return $this->addValue('string', $name, $default);
    }

    /**
     *
     * @param string $name
     * @param string $default
     * @return $this
     */
    public function addBoolean($name, $default = null)
    {
        return $this->addValue('boolean', $name, $default);
    }

    /**
     *
     * @param string $name
     * @param string $default
     * @return $this
     */
    public function addNumber($name, $default = null)
    {
        return $this->addValue('number', $name, $default);
    }

    /**
     *
     * @param string $name
     * @param string $default
     * @return $this
     */
    public function addMixed($name, $default = null)
    {
        return $this->addValue('mixed', $name, $default);
    }

    /**
     * @param string $type
     * @param string $name
     * @param mixed $default
     * @return $this
     */
    private function addValue($type, $name, $default)
    {
        $this->values[$name] = new ConfigBuilderValue($type, $default);
        return $this;
    }

    /**
     *
     * @return ConfigBuilderValue[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     *
     * @param string $name
     * @return string
     * @throws ConfigException
     */
    public function getValue($name)
    {
        if (!isset($this->values[$name])) {
            throw new ConfigException('Argument ' . $name . ' not found.');
        }
        return $this->values[$name];
    }

    /**
     *
     * @param $name
     * @param bool $required
     * @return ConfigBuilderObject
     */
    public function addObject($name,$required=true)
    {
        $this->objects[$name] = new ConfigBuilderObject($required);
        return $this->objects[$name];
    }

    /**
     *
     * @param string $name
     * @param mixed $value
     * @param int $min
     * @param int $max
     * @return ConfigBuilderArray
     */
    public function addArray($name, $value,$min=0,$max=null)
    {
        $this->arrays[$name] = new ConfigBuilderArray($value,$min,$max);
        return $this->arrays[$name];
    }

    /**
     *
     * @return ConfigBuilderArray[]
     */
    public function getArrays()
    {
        return $this->arrays;
    }

    /**
     *
     * @param string $name
     * @return ConfigBuilderArray
     * @throws ConfigException
     */
    public function getArray($name)
    {
        if (!isset($this->arrays[$name])) {
            throw new ConfigException('Array ' . $name . ' not found.');
        }
        return $this->arrays[$name];
    }

    /**
     *
     * @return ConfigBuilderObject[]
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     *
     * @param string $name
     * @return ConfigBuilderObject
     * @throws ConfigException
     */
    public function getObject($name)
    {
        if (!isset($this->objects[$name])) {
            throw new ConfigException('Object ' . $name . ' not found.');
        }
        return $this->objects[$name];
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

}