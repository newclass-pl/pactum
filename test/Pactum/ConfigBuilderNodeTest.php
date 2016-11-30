<?php

namespace Test\Pactum;

class ConfigBuilderNodeTest extends \PHPUnit_Framework_TestCase{
	
	private $node;

	protected function setUp(){
		$this->node=new ConfigBuilderNode('service');
		$this->node->addAttribute('class');
		$this->node->addAttribute('singletone',"true");
		$this->node->addAttribute('autoup',"");

		$subNode=new ConfigBuilderNode('method');

		$this->node->addNode($subNode);
	}

	public function testGetName(){
		$this->assertEquals('service',$this->node->getName());
	}

	public function testGetAttributes(){
		$getAttributes=$this->node->getAttributes();
		$this->assertCount(3,$getAttributes);
		$this->assertEquals('class',$getAttributes[0]->getName());
		$this->assertEquals('singletone',$getAttributes[1]->getName());
		$this->assertEquals('autoup',$getAttributes[2]->getName());

	}

	public function testGetAttribute(){
		$this->assertEquals('singletone',$this->node->getAttribute('singletone')->getName());

		try{
			$this->node->getAttribute('unknown');
			$this->assertTrue(false);
		}
		catch(\Exception $e){
			$this->assertTrue($e instanceof ConfigException);
		}
	}

	public function testGetNodes(){
		$nodes=$this->node->getNodes();
		$this->assertCount(1,$nodes);
		$this->assertEquals('method',$nodes[0]->getName());

	}

	public function testGetNode(){
		$this->assertEquals('method',$this->node->getNode('method')->getName());

		try{
			$this->node->getNode('node');
			$this->assertTrue(false);
		}
		catch(\Exception $e){
			$this->assertTrue($e instanceof ConfigException);
		}
	}

}