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

namespace Test\Pactum;


use Pactum\ConfigBuilderValue;
use Pactum\InvalidValueException;

/**
 * Class ConfigBuilderValueTest
 * @package Test\Pactum
 * @author Michal Tomczak (michal.tomczak@newclass.pl)
 */
class ConfigBuilderValueTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testGetters(){
        $builder=new ConfigBuilderValue('string','default');

        $this->assertEquals('string',$builder->getType());
        $this->assertEquals('default',$builder->getDefault());
        $this->assertFalse($builder->isRequired());

        $builder=new ConfigBuilderValue('number');

        $this->assertEquals('number',$builder->getType());
        $this->assertNull($builder->getDefault());
        $this->assertTrue($builder->isRequired());
    }

    /**
     *
     */
    public function testTypeString(){
        $e=$this->getException('string','data');
        $this->assertNull($e);

        $e=$this->getException('string',1);
        $this->assertEquals(InvalidValueException::class ,get_class($e));

    }

    /**
     *
     */
    public function testTypeNumber(){
        $e=$this->getException('number',12);
        $this->assertNull($e);

        $e=$this->getException('number',12.23);
        $this->assertNull($e);

        $e=$this->getException('number',"12s33");
        $this->assertEquals(InvalidValueException::class ,get_class($e));

    }

    /**
     *
     */
    public function testTypeBoolean(){
        $e=$this->getException('boolean',true);
        $this->assertNull($e);

        $e=$this->getException('boolean',false);
        $this->assertNull($e);

        $e=$this->getException('boolean',"true");
        $this->assertEquals(InvalidValueException::class ,get_class($e));

    }

    /**
     *
     */
    public function testTypeMixed(){
        $e=$this->getException('mixed',true);
        $this->assertNull($e);

        $e=$this->getException('mixed','string');
        $this->assertNull($e);

        $e=$this->getException('mixed',1132.232);
        $this->assertNull($e);

    }

    /**
     * @param string $type
     * @param mixed $default
     * @return \Exception|null
     */
    private function getException($type,$default){
        $exception=null;

        try{
            new ConfigBuilderValue($type,$default);
        }
        catch(\Exception $e){
            $exception=$e;
        }

        return $exception;

    }

}