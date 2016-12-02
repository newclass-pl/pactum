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
use Pactum\ObjectReader;

/**
 * Class JSONArrayReader
 * @package Pactum\Reader
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class JSONArrayReader implements ArrayReader
{

    /**
     * @var mixed[]
     */
    private $json;

    /**
     * @var string
     */
    private $path;

    /**
     * JSONArrayReader constructor.
     * @param mixed[] $data
     * @param string $path
     */
    public function __construct($data, $path)
    {
        $this->json=$data;
        $this->path=$path;
    }

    /**
     * @param int $index
     * @return mixed
     * @throws ElementNotFoundException
     */
    public function getValue($index)
    {
        if(!isset($this->json[$index])){
            throw new ElementNotFoundException($this->path,$index);
        }
        return $this->json[$index];
    }

    /**
     * @param int $index
     * @return ObjectReader
     * @throws ElementNotFoundException
     */
    public function getObject($index)
    {
        if(!isset($this->json[$index])){
            throw new ElementNotFoundException($this->path,$index);
        }
        return new JSONReader($this->json[$index],'o',$this->path.'['.$index.']');
    }

    /**
     * @param int $index
     * @return ArrayReader
     * @throws ElementNotFoundException
     */
    public function getArray($index)
    {
        if(!isset($this->json[$index])){
            throw new ElementNotFoundException($this->path,$index);
        }
        return new JSONArrayReader($this->json[$index],'o',$this->path.'['.$index.']');
    }

    /**
     * @return mixed[]
     */
    public function getValues()
    {
        $results=[];

        for($i=0; $i<count($this->json); $i++){
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

        for($i=0; $i<count($this->json); $i++){
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

        for($i=0; $i<count($this->json); $i++){
            $results[]=$this->getArray($i);
        }

        return $results;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}