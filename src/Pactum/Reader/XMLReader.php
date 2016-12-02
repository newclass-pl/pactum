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

namespace Pactum\Reader;
use Pactum\ArrayReader;
use Pactum\ElementNotFoundException;
use Pactum\InvalidTypeException;
use Pactum\ObjectReader;

/**
 * Xml file reader
 * @package Pactum\Reader
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class XMLReader implements ObjectReader{
	
	/**
	 *
	 * @var \SimpleXMLElement
	 */ 
	private $data;

    /**
     * @var string
     */
    private $path;

    /**
     * XMLReader constructor.
     * @param mixed $data
     * @param string $type
     * @param string $path
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     */
    public function __construct($data, $type='f',$path='root')
    {
        if($type==='f'){
            if(!file_exists($data)){
                throw new FileNotFoundException($data);
            }
            if(!is_readable($data)){
                throw new FileNotReadableException($data);
            }

            $data=file_get_contents($data);
            $type='s';
        }

        if($type==='s'){
            $this->data=new \SimpleXMLElement($data);
        }

        if($type==='o'){
            $this->data=$data;
        }

        $this->path=$path;
    }

    /**
     * @param string $name
     * @return ArrayReader
     * @throws ElementNotFoundException
     */
    public function getArray($name)
    {
        $records=[];
        $children=$this->data->children();
        foreach($children as $kChild=>$child){
            if($kChild!==$name){
                continue;
            }
            $records[]=$child;
        }

        if(!$records){
            throw new ElementNotFoundException($this->path,$name);
        }
        return new XMLArrayReader($records,$this->path.'->'.$name);
    }

    /**
     * @param string $name
     * @return ObjectReader
     * @throws ElementNotFoundException
     */
    public function getObject($name)
    {
        $children=$this->data->children();
        foreach($children as $kChild=>$child){
            if($kChild!==$name){
                continue;
            }
            return new XMLReader($child,'o',$this->path.'->'.$name);

        }

        throw new ElementNotFoundException($this->path,$name);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws ElementNotFoundException
     * @throws InvalidTypeException
     */
    public function getValue($name)
    {
        $attributes=$this->data->attributes();
        if(!isset($attributes[$name])){
            throw new ElementNotFoundException($this->path,$name);
        }
        $value=(string)$attributes[$name];

        if(substr($value,0,1)!=='!') {
            return $value;
        }

        $value=substr($value,1);
        if(preg_match('/^-{0,1}\d+\.\d+$/',$value)){
            return (float)$value;
        }
        else if(preg_match('/^-{0,1}\d+$/',$value)){
            return (int)$value;
        }
        else if(in_array($value,['true','false'],true)){
            return $value==='true';
        }
        else if($value==='null'){
            return null;
        }
        else{
            throw new InvalidTypeException($value);
        }
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}