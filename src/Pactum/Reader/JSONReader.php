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
 * Class JSONReader
 * @package Pactum\Reader
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class JSONReader implements ObjectReader
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
     * JSONReader constructor.
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

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}