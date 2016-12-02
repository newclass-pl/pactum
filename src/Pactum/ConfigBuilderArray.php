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
 * Class ConfigBuilderArray
 * @package Pactum
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class ConfigBuilderArray
{
    /**
     * @var mixed
     */
    private $value;
    /**
     * @var string
     */
    private $type;
    /**
     * @var int
     */
    private $min;
    /**
     * @var int
     */
    private $max;

    /**
     * ConfigBuilderArray constructor.
     * @param mixed $value
     * @param int $min
     * @param int $max
     */
    public function __construct($value,$min=0,$max=null)
    {
        $this->value = $value;
        $this->type = $this->parseType();
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return string
     * @throws InvalidValueException
     */
    public function parseType()
    {
        if ($this->value === null || !is_object($this->value)) {
            throw new InvalidValueException('', $this->value, 'object');
        }

        switch (get_class($this->value)) {
            case ConfigBuilderValue::class:
                /**
                 * @var ConfigBuilderValue $value
                 */
                $value = $this->value;
                return $value->getType();
            case ConfigBuilderObject::class:
                return 'object';
            case ConfigBuilderArray::class:
                return 'array';

        }

        throw new InvalidValueException('', $this->value, 'ConfigBuilderValue,ConfigBuilderObject,ConfigBuilderArray');

    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }
}