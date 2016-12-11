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
 * Class ClassBuilder
 * @package Pactum\Cache
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class ClassCache extends AbstractCache
{
    /**
     * @var string
     */
    private $namespace;
    /**
     * @var string
     */
    private $className;
    /**
     * @var null
     */
    private $interfaceName;

    /**
     * ClassBuilder constructor.
     * @param ConfigContainer $container
     * @param string $fieldName
     * @param string $className
     * @param null $interfaceName
     * @param string $namespace
     */
    public function __construct(ConfigContainer $container = null, $fieldName = '', $className = 'Config',$interfaceName=null,
                                $namespace = 'Pactum\\Cache')
    {
        $this->className = $className;
        $this->namespace = $namespace;
        parent::__construct($container, $fieldName);
        $this->interfaceName = $interfaceName;
    }

    /**
     * @return string
     */
    public function generateClass()
    {
        if ($this->value === null) {
            return '';
        }
        $head = '';
        $construct = "  public function __construct(){\n";
        $getters = '';
        $otherClass = '';
        foreach ($this->value->getValues() as $key => $value) {
            $field = new PrimitiveCache($value, $key);
            $head .= $field->generateDeclaration();
            $construct .= $field->generateDefinition();
            $getters .= $field->generateGetterHead()."\n";
            $getters .= $field->generateGetterBody();
        }

        foreach ($this->value->getArrays() as $key => $value) {
            $field = new ArrayCache($value, $key, $this->className . '_' . $key);
            $head .= $field->generateDeclaration();
            $construct .= $field->generateDefinition();
            $getters .= $field->generateGetterHead();
            $getters .= $field->generateGetterBody();
            $otherClass .= $field->generateClass();
        }

        foreach ($this->value->getObjects() as $key => $value) {
            $field = new ClassCache($value, $key, $this->className . '_' . $key);
            $head .= $field->generateDeclaration();
            $construct .= $field->generateDefinition();
            $getters .= $field->generateGetterHead();
            $getters .= $field->generateGetterBody();
            $otherClass .= $field->generateClass();
        }

        $construct .= "}\n";

        $otherClass.="class " . $this->className;
        if($this->interfaceName){
            $otherClass.=" implements ".$this->interfaceName;
        }

        return $otherClass."{\n" . $head . $construct . $getters . "}\n";

    }

    /**
     * @return string
     */
    public function generateInterface()
    {
        $getters = '';
        foreach ($this->value->getValues() as $key => $value) {
            $field = new PrimitiveCache($value, $key);
            $getters .= $field->generateGetterHead().";\n";
        }

        foreach ($this->value->getArrays() as $key => $value) {
            $field = new ArrayCache($value, $key, $this->className . '_' . $key);
            $getters .= $field->generateGetterHead().";\n";
        }

        foreach ($this->value->getObjects() as $key => $value) {
            $field = new ClassCache($value, $key, $this->className . '_' . $key);
            $getters .= $field->generateGetterHead().";\n";
        }


        return "interface ".$this->interfaceName."{\n ".$getters."}";

    }

    /**
     * @return string
     */
    public function generateDefinition()
    {
        $key = $this->filterName($this->key);
        $fieldName = lcfirst($key);

        $template = '$this->_' . $fieldName . ' =';

        if ($this->value === null) {
            $template .= 'null';
        } else {
            $template .= 'new ' . $this->className . '()';
        }
        $template .= ';';
        return $template;
    }

    /**
     * @return string
     */
    public function getReturnType()
    {
        if($this->value===null){
            return 'null';
        }

        return ($this->interfaceName?$this->interfaceName:$this->className);
    }
}