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
 * Config Argument.
 * @package Pactum
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class ConfigBuilderValue{

	/**
	 * @var bool
	 */
	private $required;

	/**
	 * @var string
	 */
	private $default;

    /**
     * @var string
     */
    private $type;

    /**
     *
     * @param string $type
     * @param string $default
     * @throws InvalidValueException
     */
	public function __construct($type,$default=null){
	    $this->type=$type;
		$required=true;

		if($default!==null){
			$this->default=$default;
			$required=false;
		}

		$this->required=$required;

        if(!$this->isValid($default)){
            throw new InvalidValueException('',$default,$this->type);
        }

    }

    /**
     * @param mixed $value
     * @return bool
     * @throws InvalidTypeException
     * @throws InvalidValueException
     */
	public function isValid($value){

        switch ($this->type){
            case 'string':
                return $value===null || is_string($value);
            case 'number':
                return $value===null || is_numeric($value);
            case 'boolean':
                return $value===null || is_bool($value);
                break;
                default:
                    throw new InvalidTypeException($this->type);

        }
    }

	/**
	 *
	 * @return bool
	 */
	public function isRequired(){
		return $this->required;
	}

	/**
	 *
	 * @return string
	 */
	public function getDefault(){
		return $this->default;
	}

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

}