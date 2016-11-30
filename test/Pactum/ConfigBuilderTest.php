<?php

namespace Test\Pactum;

use Pactum\ConfigBuilder;
use Pactum\ConfigBuilderObject;
use Pactum\ConfigBuilderValue;
use Pactum\Reader\JSONReader;
use Pactum\Reader\XMLReader;

class ConfigBuilderTest extends \PHPUnit_Framework_TestCase{

    /**
     * @var ConfigBuilder
     */
    private $config;

    public function setUp()
    {
        $this->config=new ConfigBuilder();
        $this->config->addBoolean("booleanTrue")
            ->addBoolean("booleanFalse")
            ->addNumber("number1")
            ->addNumber("number2")
            ->addString("text")
            ->addString("other")
            ->addArray("d_array",new ConfigBuilderObject('deeper'))
            ->getValue()->addString("test");

        $this->config->addArray("v_array",new ConfigBuilderValue('number'));
        $this->config->addObject("v_object")
            ->addString("v_text")
            ->addNumber("number",1233);
    }

    public function testParseXML(){

        $path=realpath(__DIR__.'/..');

        $xmlReader=new XMLReader($path.'/Asset/example1.xml');
        $this->config->addReader($xmlReader);

        $xmlReader=new XMLReader($path.'/Asset/example2.xml');
        $this->config->addReader($xmlReader);


        $container=$this->config->parse();
        $this->checkAssert($container);

    }

    private function checkAssert($container){
        $this->assertEquals(true,$container->getValue('booleanTrue'));
        $this->assertEquals(false,$container->getValue('booleanFalse'));
        $this->assertEquals(1,$container->getValue('number1'));
        $this->assertEquals(0.65,$container->getValue('number2'));
        $this->assertEquals('value text',$container->getValue('text'));
        $array=$container->getArray("d_array");
        $this->assertCount(1,$array);
        $obj=$array[0];
        $this->assertEquals('wdwd',$obj->getValue('test'));
        $array=$container->getArray('v_array');
        $this->assertCount(4,$array);
        $this->assertEquals([1,2,3,4],$array);
        $object=$container->getObject('v_object');
        $this->assertEquals('data',$object->getValue('v_text'));
        $this->assertEquals(1233,$object->getValue('number'));

    }

    public function testParseJSON(){

        $path=realpath(__DIR__.'/..');

        $xmlReader=new JSONReader($path.'/Asset/example1.json');
        $this->config->addReader($xmlReader);
        $xmlReader=new JSONReader($path.'/Asset/example2.json');
        $this->config->addReader($xmlReader);

        $container=$this->config->parse();

        $this->checkAssert($container);

    }

}