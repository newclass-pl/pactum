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


namespace Test\Pactum;


use Pactum\ConfigBuilderValue;
use Pactum\InvalidValueException;

class ConfigBuilderValueTest extends \PHPUnit_Framework_TestCase
{

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

    public function testTypeString(){
        $e=$this->getException('string','data');
        $this->assertNull($e);

        $e=$this->getException('string',1);
        $this->assertEquals(InvalidValueException::class ,get_class($e));

    }

    public function testTypeNumber(){
        $e=$this->getException('number',12);
        $this->assertNull($e);

        $e=$this->getException('number',12.23);
        $this->assertNull($e);

        $e=$this->getException('number',"12s33");
        $this->assertEquals(InvalidValueException::class ,get_class($e));

    }

    public function testTypeBoolean(){
        $e=$this->getException('boolean',true);
        $this->assertNull($e);

        $e=$this->getException('boolean',false);
        $this->assertNull($e);

        $e=$this->getException('boolean',"true");
        $this->assertEquals(InvalidValueException::class ,get_class($e));

    }

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