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
        $result=[];
        $objects=[];
        $arrays=[];
        $values=[];
        foreach($this->getObjects() as $name=>$builder){
            $objects[$name]=$this->parseObject($this->readers,$name,$builder);
        }

        $result['objects']=$objects;

        foreach($this->getValues() as $name=>$builder){
            $values[$name]=$this->parseValue($this->readers,$name,$builder);
        }

        $result['values']=$values;

        foreach($this->getArrays() as $name=>$builder){
            $arrays[$name]=$this->parseArray($this->readers,$name,$builder);
        }

        $result['arrays']=$arrays;

		return new ConfigContainer($result['objects'],$result['arrays'],$result['values']);

    }

    /**
     *
     * @param ObjectReader[] $readers
     * @param $name
     * @param ConfigBuilderValue $builder
     * @return ConfigContainer[]
     * @throws ConfigException
     * @throws InvalidValueException
     */
    private function parseValue(array $readers, $name, ConfigBuilderValue $builder){

        $value=null;
        try{
            $value=$this->readValue($readers,$name);
        }
        catch(ConfigException $e){
            if($builder->isRequired()){
                throw $e;
            }
            $value=$builder->getDefault();
        }

        if(!$builder->isValid($value)){
            throw new InvalidValueException($name,$value,$builder->getType());
        }

        return $value;
    }

    /**
     *
     * @param ObjectReader[] $readers
     * @param $name
     * @param ConfigBuilderObject $node
     * @return ConfigContainer
     */
	private function parseObject(array $readers, $name, ConfigBuilderObject $node){
	    $readers=$this->readObject($readers,$name);
        $objects=[];
        $arrays=[];
        $values=[];
        foreach($node->getObjects() as $name=>$builder){
            $objects[$name]=$this->parseObject($readers,$name,$builder);
        }

        foreach($node->getValues() as $name=>$builder){
            $values[$name]=$this->parseValue($readers,$name,$builder);
        }

        foreach($node->getArrays() as $name=>$builder){
            $arrays[$name]=$this->parseArray($readers,$name,$builder);
        }

        return new ConfigContainer($objects,$arrays,$values);
	}

    /**
     *
     * @param ArrayReader[] $readers
     * @param $name
     * @param ConfigBuilderArray $builder
     * @return ConfigContainer[]
     */
    private function parseArray(array $readers, $name, ConfigBuilderArray $builder){
        $results=[];
        $readers=$this->readArray($readers,$name);
        $type=$builder->getType();
        $builderArray=$builder->getValue();
        switch ($type){
            case 'object':
                foreach($readers[0]->getObjects() as $name=> $builder){
                    $results[$name]=$this->parseObject($readers,$name,$builderArray);
                }
                break;
            case 'array':
                foreach($readers[0]->getArrays() as $name=> $builder){
                    $results[$name]=$this->parseArray($readers,$name,$builderArray);
                }
                break;
            default:
                foreach($readers[0]->getValues() as $name=> $builder){
                    $results[$name]=$this->parseValue($readers,$name,$builderArray);
                }

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
        $records=[];
        $exception=null;
        foreach($readers as $reader){
            try{
                $records[]=$reader->getValue($name);
            }
            catch(ElementNotFoundException $e){
                $exception=$e;
            }
        }

        if(!$records){
            throw $exception;
        }

        if(count($records)>1){
            throw new TooManyElementException($name);
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
        $records=[];
        $exception=null;
        foreach($readers as $reader){
            try{
                $records[]=$reader->getObject($name);
            }
            catch(ElementNotFoundException $e){
                $exception=$e;
            }
        }

        if(!$records){
            throw $exception;
        }

        if(count($records)>1){
            throw new TooManyElementException($name);
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
        $records=[];
        $exception=null;
        foreach($readers as $reader){
            try{
                $records[]=$reader->getArray($name);
            }
            catch(ElementNotFoundException $e){
                $exception=$e;
            }
        }

        if(!$records){
            throw $exception;
        }

        if(count($records)>1){
            throw new TooManyElementException($name);
        }

        return $records;

    }

}