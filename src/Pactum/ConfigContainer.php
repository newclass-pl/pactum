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
 * Config container.
 * @package Pactum
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class ConfigContainer{
	
	/**
	 *
	 * @var mixed[]
	 */
	private $arrays=[];

	/**
	 *
	 * @var ConfigContainer[][]
	 */
	private $objects=[];

    /**
     * @var mixed[]
     */
    private $values;

    /**
	 *
	 * @param ConfigContainer[] $objects
	 * @param mixed[] $arrays
     * @param mixed[] $values
	 */
	public function __construct($objects, $arrays,$values){
		$this->objects=$objects;
		$this->arrays=$arrays;
        $this->values=$values;
	}

	/**
	 *
	 * @param string $name
	 * @return ConfigContainer
	 * @throws ConfigException
	 */	
	public function getObject($name){
		if(!isset($this->objects[$name])){
			throw new ConfigException('Object "'.$name.'" not found.');
		}
		return $this->objects[$name];
	}

    /**
     *
     * @param string $name
     * @return mixed[]
     * @throws ConfigException
     */
    public function getArray($name){
        if(!isset($this->arrays[$name])){
            throw new ConfigException('Array "'.$name.'" not found.');
        }
        return $this->arrays[$name];
    }

    /**
	 *
	 * @param string $name
	 * @return mixed
	 * @throws ConfigException
	 */	
	public function getValue($name){
		if(!isset($this->values[$name])){
			throw new ConfigException('Value "'.$name.'" not found.');
		}
		return $this->values[$name];
	}

}