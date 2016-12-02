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
 * Config builder.
 * @package Pactum
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class ConfigBuilder extends ConfigBuilderObject{

	/**
	 *
	 * @var ObjectReader[]
	 */
	private $readers=[];
    /**
     * @var callable[]
     */
    private $filters=[];

    /**
	 *
	 * @param ObjectReader $reader
	 */
	public function addReader(ObjectReader $reader){
		$this->readers[]=$reader;
	}

    /**
     * @param callable $callback
     */
	public function addFilter($callback){
        $this->filters[spl_object_hash((object)$callback)]=$callback;
    }

    /**
     *
     * @return ConfigContainer
     */
    public function parse(){
        $parser=new ConfigParser($this->readers,$this->filters,$this->getObjects(),$this->getArrays(),$this->getValues());

        return $parser->execute();

    }

}