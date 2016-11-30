<?php
/**
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Michal Tomczak <michal.tomczak@newaxis.pl>
 *
 * @copyright     Copyright (c) Newaxis (http://newaxis.pl)
 * @link          https://cogitary-polisy.aria.pl
 * @license       http://www.binpress.com/license/view/l/b0e782df3e50d424a32d613af2c4937b
 */


namespace Pactum;


class ConfigBuilderArray
{
    private $value;
    private $type;

    /**
     * ConfigBuilderArray constructor.
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value=$value;
        $this->type=$this->parseType();
    }

    public function parseType(){
        if($this->value===null || !is_object($this->value)){
            throw new InvalidValueException($this->value,'object');
        }

        switch(get_class($this->value)){
            case ConfigBuilderValue::class:
                /**
                 * @var ConfigBuilderValue $value
                 */
                $value=$this->value;
                return $value->getType();
            case ConfigBuilderObject::class:
                return 'object';
            case ConfigBuilderArray::class:
                return 'array';

        }
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
}