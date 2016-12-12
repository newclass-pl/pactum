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


use Pactum\ConfigBuilderArray;
use Pactum\ConfigBuilderObject;
use Pactum\ConfigBuilderValue;
use Pactum\ConfigException;

/**
 * Class ClassBuilder
 * @package Pactum\Cache
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class ClassCache
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
     * @var mixed
     */
    private $directory;
    private $elements;

    /**
     * ClassBuilder constructor.
     * @param mixed $directory
     * @param string $namespace
     * @param $className
     * @param ConfigBuilderObject[] $objects
     * @param ConfigBuilderArray[] $arrays
     * @param ConfigBuilderValue[] $values
     */
    public function __construct($directory, $namespace, $className, $objects, $arrays, $values)
    {
        $this->className = $className;
        $this->namespace = $namespace;
        $this->directory = $directory;

        $this->elements = array_merge($objects, $arrays, $values);
    }

    public function generateClass()
    {

        $template = "<?php\n";
        $template .= "namespace " . $this->namespace . ";\n";
        $template .= "\n\n";
        $template .= "class " . $this->className . "\n";
        $template .= "{\n";

        $template .= "    private \$data=[];\n";
        $template .= "    private \$cached=[];\n";
        $template .= "    public function __construct(\$data){\n";
        $template .= "        \$this->data=\$data;\n";
        $template .= "    }\n";
        foreach ($this->elements as $key => $value) {
            $template .= $this->generateGetter($key, $value);
        }

        $template .= "}\n";
        if(!file_exists($this->directory) && !mkdir($this->directory,0755,true)){
            throw new ConfigException('Can\'t create directory "'.$this->directory.'".');
        }
        file_put_contents($this->directory . '/' . $this->className . '.php', $template);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function filterName($name)
    {
        $parts = preg_split('/[_-]/', $name);
        $camelCase = '';
        foreach ($parts as $part) {
            $camelCase .= ucfirst($part);
        }

        return preg_replace('/[^a-zA-Z0-9]/', '', $camelCase);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return string
     */
    public function generateGetter($key, $value)
    {
        $methodName = $this->filterName($key);
        $returnType = $this->generateReturnType($methodName, $value);
        $prefixMethodName = $returnType === 'boolean' ? 'is' : 'get';
        $template = "   /** @return " . $returnType . " */\n";
        $template .= "    public function " . $prefixMethodName . $methodName . "()\n";
        $template .= "    {\n";
        $template .= "        if(array_key_exists('" . $key . "',\$this->cached))\n";
        $template .= "        {\n";
        $template .= "            return \$this->cached['" . $key . "'];\n";
        $template .= "        }\n";

        $template .= $this->generateGetterValue($key, $value);

        $template .= "       \$this->cached['" . $key . "']=\$value;\n";
        $template .= "       return \$value;\n";

        $template .= "    }\n";

        return $template;
    }

    private function generateReturnType($methodName, $value)
    {
        if ($value instanceof ConfigBuilderValue) {
            return $value->getType();
        }

        if ($value instanceof ConfigBuilderObject) {

            $namespace = $this->namespace . '\\' . $this->className;
                $subclass=new ClassCache($this->directory . '/' . $this->className, $namespace, $methodName, $value->getObjects(),
                    $value->getArrays(), $value->getValues());
                $subclass->generateClass();

            return $namespace . '\\' . $methodName;
        }

        if ($value instanceof ConfigBuilderArray) {
            return $this->generateReturnType($methodName,$value->getValue()).'[]';
        }

        return 'null';
    }

    private function generateGetterValue($key, $value,$array=false)
    {
        if ($value instanceof ConfigBuilderObject) {
            $methodName = $this->filterName($key);

            $namespace = $this->namespace . '\\' . $this->className;
            $template = "";

            if($array){
                $template.= "        \$value=[];\n";
                $template.= "        foreach(\$this->data['" . $key . "'] as \$record)\n";
                $template.= "        {\n";
                $template.= "            \$value[]=new \\".$namespace . '\\' . $methodName."(\$record);\n";
                $template.= "        }\n";
                return $template;
            }

            $template = "        \$value=new \\".$namespace . '\\' . $methodName."(\$this->data['" . $key . "']);\n";
            return $template;
        }

        if ($value instanceof ConfigBuilderArray) {
            return $this->generateGetterValue($key,$value->getValue(),true);
        }

        $template = "        \$value=\$this->data['" . $key . "'];\n";
        return $template;
    }

}