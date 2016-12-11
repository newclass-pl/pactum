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

/**
 * Class AbstractCache
 * @package Pactum\Cache
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
abstract class AbstractCache
{
    /**
     * @var null|string
     */
    protected $key;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var mixed
     */
    protected $value;

    /**
     * FieldBuilder constructor.
     * @param mixed $value
     * @param int|string $key
     */
    public function __construct($value, $key)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    abstract public function generateDefinition();

    /**
     * @return string
     */
    abstract public function getReturnType();

    /**
     * @return string
     */
    public function generateDeclaration(){
        $key=$this->filterName($this->key);
        $fieldName=lcfirst($key);

        $template="/** @var ".$this->getReturnType()." */\nprivate \$_".$fieldName.";\n";
        return $template;
    }

    /**
     * @return string
     */
    public function generateGetterHead(){
        $key=$this->filterName($this->key);

        $template="/** @return ".$this->getReturnType()." */public function get".$key."()";
        return $template;
    }

    public function generateGetterBody(){
        $key=$this->filterName($this->key);
        $fieldName=lcfirst($key);

        $templage="{\nreturn \$this->_".$fieldName.";\n}";
        return $templage;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function filterName($name){
        $parts=preg_split('/[_-]/',$name);
        $camelCase='';
        foreach ($parts as $part){
            $camelCase.=ucfirst($part);
        }

        return preg_replace('/[^a-zA-Z0-9]/','',$camelCase);
    }

}