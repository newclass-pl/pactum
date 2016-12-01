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
 * Class XMLArrayReader
 * @package Pactum\Reader
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class XMLArrayReader implements ArrayReader
{

    /**
     * @var \SimpleXMLElement[]
     */
    private $data;

    /**
     * @var string
     */
    private $path;

    /**
     * XMLArrayReader constructor.
     * @param \SimpleXMLElement[] $data
     * @param string $path
     */
    public function __construct($data, $path)
    {
        $this->data=$data;
        $this->path=$path;
    }

    /**
     * @param int $index
     * @return mixed
     * @throws ElementNotFoundException
     * @throws InvalidTypeException
     */
    public function getValue($index)
    {
        if(!isset($this->data[$index])){
            throw new ElementNotFoundException($this->path,$index);
        }
        $attributes=$this->data[$index]->attributes();

        $value=(string)$attributes['value'];
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
     * @param int $index
     * @return ObjectReader
     * @throws ElementNotFoundException
     */
    public function getObject($index)
    {
        if(!isset($this->data[$index])){
            throw new ElementNotFoundException($this->path,$index);
        }
        return new XMLReader($this->data[$index],'o',$this->path.'['.$index.']');
    }

    /**
     * @param int $index
     * @return ArrayReader
     * @throws ElementNotFoundException
     */
    public function getArray($index)
    {
        if(!isset($this->data[$index])){
            throw new ElementNotFoundException($this->path,$index);
        }
        return new XMLArrayReader($this->data[$index],$this->path.'['.$index.']');
    }

    /**
     * @return mixed[]
     */
    public function getValues()
    {
        $results=[];

        for($i=0; $i<count($this->data); $i++){
            $results[]=$this->getValue($i);
        }

        return $results;
    }

    /**
     * @return ObjectReader[]
     */
    public function getObjects()
    {
        $results=[];

        for($i=0; $i<count($this->data); $i++){
            $results[]=$this->getObject($i);
        }

        return $results;
    }

    /**
     * @return ArrayReader[]
     */
    public function getArrays()
    {
        $results=[];

        for($i=0; $i<count($this->data); $i++){
            $results[]=$this->getArray($i);
        }

        return $results;
    }
}