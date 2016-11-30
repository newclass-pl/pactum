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

class JSONReader implements ObjectReader
{

    private $json;
    private $path;

    public function __construct($data, $type='f',$path='')
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
            $path=$data;
        }

        if($type==='s'){
            $this->json=json_decode($data,true);
        }

        if($type==='o'){
            $this->json=$data;
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
        if(!isset($this->json[$name])){
            throw new ElementNotFoundException($this->path,$name);
        }
        return new JSONArrayReader($this->json[$name],'o',$this->path.'->'.$name);
    }

    /**
     * @param string $name
     * @return ObjectReader
     * @throws ElementNotFoundException
     */
    public function getObject($name)
    {
        if(!isset($this->json[$name])){
            throw new ElementNotFoundException($this->path,$name);
        }
        return new JSONReader($this->json[$name],'o',$this->path.'->'.$name);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws ElementNotFoundException
     */
    public function getValue($name)
    {
        if(!isset($this->json[$name])){
            throw new ElementNotFoundException($this->path,$name);
        }
        return $this->json[$name];
    }
}