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
 * Class ParserProcess
 * @package Pactum
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class ParserProcess
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $path;
    /**
     * @var object
     */
    private $builder;
    /**
     * @var mixed
     */
    private $value;
    /**
     * @var string
     */
    private $name;
    /**
     * @var ConfigParser
     */
    private $parser;

    /**
     * ParserProcess constructor.
     * @param ConfigParser $parser
     * @param string $type
     * @param string $path
     * @param string $name
     * @param object $builder
     * @param mixed $value
     */
    public function __construct(ConfigParser $parser,$type, $path, $name, $builder, $value)
    {
        $this->type = $type;
        $this->path = $path;
        $this->builder = $builder;
        $this->value = $value;
        $this->name = $name;
        $this->parser = $parser;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return object
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param ObjectReader $reader
     */
    public function addReader(ObjectReader $reader)
    {
        $this->parser->addReader($reader);
    }

}