<?php

namespace Test\Pactum;

use Pactum\ConfigBuilder;
use Pactum\ConfigBuilderObject;
use Pactum\ConfigBuilderValue;
use Pactum\Reader\JSONReader;
use Pactum\XmlFileReader;

class ConfigBuilderTest extends \PHPUnit_Framework_TestCase{

    
	public function testParseXML(){

        $path=realpath(__DIR__.'/..');
        $config=new ConfigBuilder();

        $xmlReader=new XmlFileReader($path.'/Asset/example1.xml');
        $config->addReader($xmlReader);

        $xmlReader=new XmlFileReader($path.'/Asset/example2.xml');
        $config->addReader($xmlReader);

        $node=new ConfigBuilderObject('node');
        $node->addAttribute('var1')
            ->addAttribute('var2')
            ->createNode('sub-node')
            ->addAttribute('var1')
            ->addAttribute('var2','default');

        $element=new ConfigBuilderObject('element');
        $element->addAttribute('var1');

        $config->addNode($node);
        $config->addNode($element);

        $container=$config->parse();

		$nodes=$container->getObjects('node');
        $this->assertCount(1,$nodes);
        $node=$nodes[0];
        $this->assertEquals('value1',$node->getAttribute('var1'));
        $this->assertEquals('value2',$node->getAttribute('var2'));

        $subNodes=$node->getObjects('sub-node');
        $this->assertCount(2,$subNodes);
        $subNode=$subNodes[0];
        $this->assertEquals('sn1',$subNode->getAttribute('var1'));
        $this->assertEquals('default',$subNode->getAttribute('var2'));
        $subNode=$subNodes[1];
        $this->assertEquals('sn2',$subNode->getAttribute('var1'));
        $this->assertEquals('sn2d',$subNode->getAttribute('var2'));


    }

    public function testParseJSON(){

        $path=realpath(__DIR__.'/..');
        $config=new ConfigBuilder();

        $xmlReader=new JSONReader($path.'/Asset/example1.json');
        $config->addReader($xmlReader);

        $config->addBoolean("booleanTrue")
        ->addBoolean("booleanFalse")
        ->addNumber("number1")
        ->addNumber("number2")
        ->addString("text")
        ->addArray("d_array",new ConfigBuilderObject('deeper'))
            ->getValue()->addString("test");

        $config->addArray("v_array",new ConfigBuilderValue('number'));
        $config->addObject("v_object")
        ->addString("v_text")
            ->addNumber("number",1233);

        $container=$config->parse();
        var_dump($container);
//
//        $nodes=$container->getNodes('node');
//        $this->assertCount(1,$nodes);
//        $node=$nodes[0];
//        $this->assertEquals('value1',$node->getAttribute('var1'));
//        $this->assertEquals('value2',$node->getAttribute('var2'));
//
//        $subNodes=$node->getNodes('sub-node');
//        $this->assertCount(2,$subNodes);
//        $subNode=$subNodes[0];
//        $this->assertEquals('sn1',$subNode->getAttribute('var1'));
//        $this->assertEquals('default',$subNode->getAttribute('var2'));
//        $subNode=$subNodes[1];
//        $this->assertEquals('sn2',$subNode->getAttribute('var1'));
//        $this->assertEquals('sn2d',$subNode->getAttribute('var2'));


    }

}