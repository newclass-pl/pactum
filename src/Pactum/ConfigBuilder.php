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
 *
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class ConfigBuilder extends ConfigBuilderObject{

	/**
	 *
	 * @var ObjectReader[]
	 */
	private $readers=[];

	/**
	 *
	 * @param ObjectReader $reader
	 */
	public function addReader(ObjectReader $reader){
		$this->readers[]=$reader;
	}

	/**
	 *
	 * @return ConfigContainer
	 */
	public function parse(){
		$data=[];

		foreach($this->readers as $reader){
			$data=$this->mergeNodes($data,$this->parseReader($reader));
		}

		return new ConfigContainer($data['objects'],$data['arrays'],$data['values']);
	}

	/**
	 *
	 * @param ConfigContainer[][] $originNode
	 * @param ConfigContainer[] $newNodes
	 * @return ConfigContainer[][]
	 */
	private function mergeNodes($originNode,$newNodes){
		foreach($newNodes as $nodeName=>$node){
			if(!isset($originNode[$nodeName])){
				$originNode[$nodeName]=[];
			}

			$originNode[$nodeName]=array_merge($originNode[$nodeName],$node);
		}

		return $originNode;

	}

	/**
	 *
	 * @param ObjectReader $reader
	 * @return ConfigContainer[]
	 */
	private function parseReader(ObjectReader $reader){
	    $result=[];
		$objects=[];
		$arrays=[];
		$values=[];
		foreach($this->getObjects() as $name=>$builder){
			$objects[$name]=$this->parseObject($reader,$name,$builder);
		}

		$result['objects']=$objects;

        foreach($this->getValues() as $name=>$builder){
            $values[$name]=$this->parseValue($reader,$name,$builder);
        }

        $result['values']=$values;

        foreach($this->getArrays() as $name=>$builder){
            $arrays[$name]=$this->parseArray($reader,$name,$builder);
        }

        $result['arrays']=$arrays;

        return $result;
	}

    /**
     *
     * @param ObjectReader $reader
     * @param $name
     * @param ConfigBuilderValue $builder
     * @return ConfigContainer[]
     * @throws ConfigException
     * @throws InvalidValueException
     */
    private function parseValue(ObjectReader $reader, $name,ConfigBuilderValue $builder){

        $value=null;
        try{
            $value=$reader->getValue($name);
        }
        catch(ConfigException $e){
            if($builder->isRequired()){
                throw $e;
            }
            $value=$builder->getDefault();
        }

        if(!$builder->isValid($value)){
            throw new InvalidValueException($value,$builder->getType());
        }

        return $value;
    }

    /**
     *
     * @param ObjectReader $reader
     * @param $name
     * @param ConfigBuilderObject $node
     * @return ConfigContainer
     */
	private function parseObject(ObjectReader $reader, $name,ConfigBuilderObject $node){
	    $reader=$reader->getObject($name);
        $objects=[];
        $arrays=[];
        $values=[];
        foreach($node->getObjects() as $name=>$builder){
            $objects[$name]=$this->parseObject($reader,$name,$builder);
        }

        foreach($node->getValues() as $name=>$builder){
            $values[$name]=$this->parseValue($reader,$name,$builder);
        }

        foreach($node->getArrays() as $name=>$builder){
            $arrays[$name]=$this->parseArray($reader,$name,$builder);
        }

        return new ConfigContainer($objects,$arrays,$values);
	}

    /**
     *
     * @param ObjectReader $reader
     * @param $name
     * @param ConfigBuilderArray $builder
     * @return ConfigContainer[]
     */
    private function parseArray(ObjectReader $reader, $name,ConfigBuilderArray $builder){
        $results=[];
        $reader=$reader->getArray($name);
        $type=$builder->getType();
        $builderArray=$builder->getValue();
        switch ($type){
            case 'object':
                foreach($reader->getObjects() as $name=>$builder){
                    $results[$name]=$this->parseObject($reader,$name,$builderArray);
                }
                break;
            case 'array':
                foreach($reader->getArrays() as $name=>$builder){
                    $results[$name]=$this->parseArray($reader,$name,$builderArray);
                }
                break;
            default:
                foreach($reader->getValues() as $name=>$builder){
                    $results[$name]=$this->parseValue($reader,$name,$builderArray);
                }

        }
        return $results;
    }

}