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
	private $data=[];
    /**
     * @var mixed[]
     */
    private $cached=[];

    /**
     *
     * @param mixed[] $data
     */
	public function __construct($data){
		$this->data=$data;
	}

	/**
	 *
	 * @param string $name
	 * @return \mixed[]
     * @throws ConfigException
	 */	
	public function getData($name){
        if(array_key_exists($name,$this->cached)){
            return $this->cached[$name];
        }

		if(!array_key_exists($name,$this->data)){
			throw new ConfigException('Value "'.$name.'" not found.');
		}

		$value=$this->data[$name];
		if(is_array($value)){
            $value=$this->filterArray($value);
        }

        $this->cached[$name]=$value;

        return $value;
	}

    /**
     * @param mixed $arr
     * @return bool
     */
    private function isAssoc($arr)
    {
        if(!is_array($arr)){
            return false;
        }
        if (array() === $arr){
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    private function filterArray($data)
    {
        if(!$data){
            return [];
        }

        if($this->isAssoc($data)){
            return new ConfigContainer($data);
        }

        $value=$data[0];
        if(!is_array($value)){
            return $data;
        }

        if(!$this->isAssoc($value)){
            return $data;
        }

        foreach($data as &$value){
            $value=new ConfigContainer($value);
        }
        return $data;

    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize($this);
    }
}