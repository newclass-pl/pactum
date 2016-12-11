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


namespace Pactum\Cache;


use Pactum\ConfigContainer;

/**
 * Class ArrayBuilder
 * @package Pactum\Cache
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class ArrayCache extends AbstractCache
{
    private $className;

    /**
     * ClassBuilder constructor.
     * @param ConfigContainer $value
     * @param string $fieldName
     * @param string $className
     */
    public function __construct($value, $fieldName, $className)
    {
        $this->className = $className;
        parent::__construct($value, $fieldName);
    }

    public function generateDefinition()
    {
        $key = $this->filterName($this->key);
        $fieldName = lcfirst($key);

        $template = "\$this->_" . $fieldName . " = [];\n";
        foreach ($this->value as $key => $record) {
            if (is_object($record)) {
                $template .= '$this->_' . $fieldName . '[] = new ' . $this->className . '_' . $key . '();';
            } else {
                $field = new PrimitiveCache($record, $key);
                $template .= '$this->_' . $fieldName . '[] = ' . $field->filterValue($record) . ';';
            }

            $template .= "\n";
        }
        return $template;
    }

    public function generateClass()
    {
        $template = '';
        if(!$this->value){
            return $template;
        }

        $value=$this->value[0];
        if(!is_object($value)){
            return $template;

        }

        $interface=new ClassCache($value, $this->key, $this->className.'_0',$this->className . 'Interface');
        $template.=$interface->generateInterface();

        foreach ($this->value as $key => $record) {
            $field = new ClassCache($record, $this->key, $this->className . '_' . $key,$this->className . 'Interface');
            $template .= $field->generateClass();
        }

        return $template;
    }

    /**
     * @return string
     */
    public function getReturnType()
    {
        if(count($this->value)===0){
            return 'null';
        }

        $value=$this->value[0];
        if(is_object($value)){
            return $this->className.'Interface[]';
        }

        if(is_string($value)){
            return 'string[]';
        }

        if(is_bool($value)){
            return 'bool[]';
        }

        if(is_float($value)){
            return 'float[]';
        }

        if(is_int($value)){
            return 'int[]';
        }

        return 'mixed[]';

    }
}