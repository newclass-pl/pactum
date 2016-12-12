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
    public function __construct($readers, $filters, $objects, $arrays, $values)
    {
        $this->objects = $objects;
        $this->arrays = $arrays;
        $this->values = $values;
        $this->readers = $readers;
        $this->filters = $filters;
    }

    /**
     * @return mixed[]
     */
    public function execute()
    {
        if (!$this->readers) {
            return [];
        }

        $result = [];

        foreach ($this->arrays as $name => $builder) {
            $result[$name] = $this->parseArray($this->readers, $name, $builder);
        }

        foreach ($this->objects as $name => $builder) {
            $result[$name] = $this->parseObject($this->readers, $name, $builder);
        }

        foreach ($this->values as $name => $builder) {
            $result[$name] = $this->parseValue($this->readers, $name, $builder);
        }

        return $result;

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
        $exception = null;
        $value = null;
        try {
            $value = $this->readValue($readers, $name);
        } catch (ElementNotFoundException $e) {
            $exception = $e;
        }

        $process = new ParserProcess($this, $builder->getType(), $readers[0]->getPath(), $name, $builder, $value);

        $this->fireFilters($process);

        $value = $process->getValue();

        if ($exception !== null && $value === null) {
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
     * @return mixed[]
     * @throws ElementNotFoundException
     */
    private function parseObject(array $readers, $name, ConfigBuilderObject $builder)
    {
        try {$readers = $this->readObject($readers, $name);
        } catch (ElementNotFoundException $e) {
            if(!$builder->isRequired()){
                return null;
            }

            throw $e;
        }

        $results=[];

        foreach ($builder->getArrays() as $name => $subBuilder) {
            $results[$name] = $this->parseArray($readers, $name, $subBuilder);
        }

        foreach ($builder->getObjects() as $name => $subBuilder) {
            $results[$name] = $this->parseObject($readers, $name, $subBuilder);
        }

        foreach ($builder->getValues() as $name => $subBuilder) {
            $results[$name] = $this->parseValue($readers, $name, $subBuilder);
        }

        $process = new ParserProcess($this, 'object', $readers[0]->getPath(), $name, $builder, $results);

        $this->fireFilters($process);
        $value = $process->getValue();

        return $value;
    }

    /**
     *
     * @param ArrayReader[] $readers
     * @param string $name
     * @param ConfigBuilderArray $builder
     * @return mixed[]
     * @throws ConfigException
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
                throw new ConfigException('Deep array is not supported. Use object instead array.');

            case 'object':
                foreach ($readers as $reader) {
                    foreach ($reader->getObjects() as $name => $subBuilder) {
                        $results[] = $this->parseObject([$reader], $name, $builderArray);
                    }
                }
                break;
            default:
                foreach ($readers as $reader) {
                    foreach ($reader->getValues() as $name => $subBuilder) {
                        $results[] = $this->parseValue([$reader], $name, $builderArray);
                    }
                }

        }

        $process = new ParserProcess($this, 'array', $readers[0]->getPath(), $name, $builder, $results);

        $this->fireFilters($process);
        $results = $process->getValue();

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
     * @return mixed[]
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

        return $records;

    }

    /**
     * @param ParserProcess $process
     */
    private function fireFilters($process)
    {
        foreach ($this->filters as $filter) {
            call_user_func($filter, $process);
        }
    }

    /**
     * @param ObjectReader $reader
     */
    public function addReader($reader)
    {
        $this->readers[] = $reader;
    }
}