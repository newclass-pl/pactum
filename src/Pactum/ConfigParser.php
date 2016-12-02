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
 * Class ConfigParser
 * @package Pactum
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class ConfigParser
{
    /**
     * @var ConfigBuilderObject[]
     */
    private $objects;
    /**
     * @var ConfigBuilderArray[]
     */
    private $arrays;
    /**
     * @var ConfigBuilderValue[]
     */
    private $values;
    /**
     * @var ObjectReader[]
     */
    private $readers;
    /**
     * @var callable[]
     */
    private $filters;

    /**
     * ConfigParser constructor.
     * @param ObjectReader[] $readers
     * @param callable[] $filters
     * @param ConfigBuilderObject[] $objects
     * @param ConfigBuilderArray[] $arrays
     * @param ConfigBuilderValue[] $values
     */
    public function __construct($readers,$filters, $objects, $arrays, $values)
    {
        $this->objects = $objects;
        $this->arrays = $arrays;
        $this->values = $values;
        $this->readers = $readers;
        $this->filters = $filters;
    }

    /**
     * @return ConfigContainer
     */
    public function execute()
    {
        if(!$this->readers){
            return new ConfigContainer([],[],[]);
        }

        $result = [];
        $objects = [];
        $arrays = [];
        $values = [];

        foreach ($this->arrays as $name => $builder) {
            $arrays[$name] = $this->parseArray($this->readers, $name, $builder);
        }

        $result['arrays'] = $arrays;

        foreach ($this->objects as $name => $builder) {
            $objects[$name] = $this->parseObject($this->readers, $name, $builder);
        }

        $result['objects'] = $objects;

        foreach ($this->values as $name => $builder) {
            $values[$name] = $this->parseValue($this->readers, $name, $builder);
        }

        $result['values'] = $values;

        return new ConfigContainer($result['objects'], $result['arrays'], $result['values']);

    }

    /**
     *
     * @param ObjectReader[] $readers
     * @param string $name
     * @param ConfigBuilderValue $builder
     * @return mixed
     * @throws ConfigException
     * @throws InvalidValueException
     */
    private function parseValue(array $readers, $name, ConfigBuilderValue $builder)
    {
        $exception=null;
        $value = null;
        try {
            $value = $this->readValue($readers, $name);
        } catch (ElementNotFoundException $e) {
            $exception=$e;
        }

        $process=new ParserProcess($this,$builder->getType(),$readers[0]->getPath(),$name,$builder,$value);

        $this->fireFilters($process);

        $value=$process->getValue();

        if($exception!==null && $value===null){
            if ($builder->isRequired()) {
                throw $exception;
            }
            $value = $builder->getDefault();
        }

        if (!$builder->isValid($value)) {
            throw new InvalidValueException($name, $value, $builder->getType());
        }

        return $value;
    }

    /**
     *
     * @param ObjectReader[] $readers
     * @param string $name
     * @param ConfigBuilderObject $builder
     * @return ConfigContainer
     */
    private function parseObject(array $readers, $name, ConfigBuilderObject $builder)
    {
        $readers = $this->readObject($readers, $name);
        $objects = [];
        $arrays = [];
        $values = [];

        foreach ($builder->getArrays() as $name => $subBuilder) {
            $arrays[$name] = $this->parseArray($readers, $name, $subBuilder);
        }

        foreach ($builder->getObjects() as $name => $subBuilder) {
            $objects[$name] = $this->parseObject($readers, $name, $subBuilder);
        }

        foreach ($builder->getValues() as $name => $subBuilder) {
            $values[$name] = $this->parseValue($readers, $name, $subBuilder);
        }

        $value=new ConfigContainer($objects, $arrays, $values);
        $process=new ParserProcess($this,'object',$readers[0]->getPath(),$name,$builder,$value);

        $this->fireFilters($process);
        $value=$process->getValue();

        return $value;
    }

    /**
     *
     * @param ArrayReader[] $readers
     * @param string $name
     * @param ConfigBuilderArray $builder
     * @return ConfigContainer[]
     * @throws ElementNotFoundException
     * @throws InvalidNumberElementException
     */
    private function parseArray(array $readers, $name, ConfigBuilderArray $builder)
    {
        $results = [];
        try {
            $readers = $this->readArray($readers, $name);
        } catch (ElementNotFoundException $e) {
            if ($builder->getMin() !== 0) {
                throw $e;
            }
            return $results;
        }

        $type = $builder->getType();
        $builderArray = $builder->getValue();
        switch ($type) {
            case 'array':
                foreach ($readers[0]->getArrays() as $name => $subBuilder) {
                    $results[$name] = $this->parseArray($readers, $name, $builderArray);
                }
                break;
            case 'object':
                foreach ($readers[0]->getObjects() as $name => $subBuilder) {
                    $results[$name] = $this->parseObject($readers, $name, $builderArray);
                }
                break;
            default:
                foreach ($readers[0]->getValues() as $name => $subBuilder) {
                    $results[$name] = $this->parseValue($readers, $name, $builderArray);
                }

        }

        $process=new ParserProcess($this,'array',$readers[0]->getPath(),$name,$builder,$results);

        $this->fireFilters($process);
        $results=$process->getValue();

        $resultNumber = count($results);
        if ($builder->getMin() > $resultNumber) {
            throw new InvalidNumberElementException($readers[0]->getPath(), $builder->getMin(), $resultNumber);
        }

        if ($builder->getMax() !== null && $resultNumber > $builder->getMax()) {
            throw new InvalidNumberElementException($readers[0]->getPath(), $builder->getMax(), $resultNumber);
        }

        return $results;
    }

    /**
     * @param ObjectReader[] $readers
     * @param string $name
     * @return mixed
     * @throws ElementNotFoundException
     * @throws TooManyElementException
     */
    private function readValue(array $readers, $name)
    {
        $records = [];
        $exception = null;
        foreach ($readers as $reader) {
            try {
                $records[] = $reader->getValue($name);
            } catch (ElementNotFoundException $e) {
                $exception = $e;
            }
        }

        if (!$records) {
            throw $exception;
        }

        if (count($records) > 1) {
            throw new TooManyElementException($readers[0]->getPath(), $name);
        }
        return $records[0];

    }

    /**
     * @param ObjectReader[] $readers
     * @param string $name
     * @return mixed
     * @throws ElementNotFoundException
     * @throws TooManyElementException
     */
    private function readObject(array $readers, $name)
    {
        $records = [];
        $exception = null;
        foreach ($readers as $reader) {
            try {
                $records[] = $reader->getObject($name);
            } catch (ElementNotFoundException $e) {
                $exception = $e;
            }
        }

        if (!$records) {
            throw $exception;
        }

        if (count($records) > 1) {
            throw new TooManyElementException($readers[0]->getPath(), $name);
        }

        return $records;

    }

    /**
     * @param ObjectReader[] $readers
     * @param string $name
     * @return mixed
     * @throws ElementNotFoundException
     * @throws TooManyElementException
     */
    private function readArray(array $readers, $name)
    {
        $records = [];
        $exception = null;
        foreach ($readers as $reader) {
            try {
                $records[] = $reader->getArray($name);
            } catch (ElementNotFoundException $e) {
                $exception = $e;
            }
        }

        if (!$records) {
            throw $exception;
        }

        if (count($records) > 1) {
            throw new TooManyElementException($readers[0]->getPath(), $name);
        }

        return $records;

    }

    /**
     * @param ParserProcess $process
     */
    private function fireFilters($process)
    {
        foreach($this->filters as $filter){
            call_user_func($filter,$process);
        }
    }

    /**
     * @param ObjectReader $reader
     */
    public function addReader($reader)
    {
        $this->readers[]=$reader;
    }
}