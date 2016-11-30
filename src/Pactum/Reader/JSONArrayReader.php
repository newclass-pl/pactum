<?php
/**
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Michal Tomczak <michal.tomczak@newaxis.pl>
 *
 * @copyright     Copyright (c) Newaxis (http://newaxis.pl)
 * @link          https://cogitary-polisy.aria.pl
 * @license       http://www.binpress.com/license/view/l/b0e782df3e50d424a32d613af2c4937b
 */


namespace Pactum\Reader;


use Pactum\ArrayReader;
use Pactum\ElementNotFoundException;
use Pactum\ObjectReader;

class JSONArrayReader implements ArrayReader
{

    private $json;
    private $path;

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
}